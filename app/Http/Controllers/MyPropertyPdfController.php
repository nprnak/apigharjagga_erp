<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MyPropertyPdfController extends Controller
{
    /**
     * The logged-in user's own Annex-A property record — scoped so one
     * user can never download another owner's listing PDF by guessing an
     * id (an Agent may also download listings for owners they hold an
     * approved Power of Attorney for, mirroring MyPropertyResource's own
     * viewing scope).
     */
    public function download(Request $request, Property $property): Response
    {
        // Uploaded property photos can be several megapixels each; downscale
        // before embedding so DomPDF never has to load full-resolution
        // originals into memory (same approach as KycController::downloadPdf()).
        ini_set('memory_limit', '512M');

        $user = $request->user();
        $approvedOwnerClientIds = $user->approvedPoaOwnerClientIds();

        $isOwnRecord = $property->user_id === $user->id
            || ($approvedOwnerClientIds !== [] && in_array($property->owner_client_id, $approvedOwnerClientIds, true));

        abort_unless($isOwnRecord, 403);

        $property->load(['address', 'photos', 'documents.docType', 'features', 'listings.assignedOfficer', 'owner', 'user.kycVerification']);

        $photoData = [];
        foreach ($property->photos as $photo) {
            $photoData[$photo->photo_id] = $this->resizedImageDataUri($photo->file_ref);
        }

        $listing = $property->listings->sortByDesc('listing_id')->first();

        $pdf = Pdf::loadView('pdf.my_property_listing_record', [
            'property' => $property,
            'photoData' => $photoData,
            'signatureData' => $this->resizedImageDataUri($listing?->applicant_signature_path, 300),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("property-{$property->property_code}.pdf");
    }

    /**
     * Downscale a stored public-disk image to a max dimension and return it
     * as a base64 data URI, so DomPDF never has to load the original
     * full-resolution upload into memory. Identical helper to
     * KycController::resizedImageDataUri().
     */
    private function resizedImageDataUri(?string $path, int $maxDim = 500): ?string
    {
        if (! $path) {
            return null;
        }

        $fullPath = Storage::disk('public')->path($path);
        if (! is_file($fullPath)) {
            return null;
        }

        $info = @getimagesize($fullPath);
        if (! $info) {
            return null;
        }

        [$width, $height, $type] = $info;

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => @imagecreatefrompng($fullPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : null,
            default => null,
        };

        if (! $source) {
            return null;
        }

        $scale = min(1, $maxDim / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);
        imagefill($resized, 0, 0, imagecolorallocate($resized, 255, 255, 255));
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagedestroy($source);

        ob_start();
        imagejpeg($resized, null, 82);
        $data = ob_get_clean();
        imagedestroy($resized);

        return 'data:image/jpeg;base64,'.base64_encode($data);
    }
}
