<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function downloadPdf(int $id): Response
    {
        // Uploaded KYC photos can be several megapixels; DomPDF decodes the
        // full bitmap into memory when embedding an image by path, which can
        // exhaust the PHP memory limit. Downscaling first keeps memory bounded.
        ini_set('memory_limit', '512M');

        $kyc = KycVerification::with(['user', 'documents.docType', 'verifiedBy', 'approvedBy'])->findOrFail($id);

        return $this->renderPdf($kyc);
    }

    /**
     * The logged-in user's own certificate — scoped to their own record so
     * one user can never download another's KYC PDF by guessing an id.
     */
    public function downloadMyPdf(Request $request): Response
    {
        ini_set('memory_limit', '512M');

        $user = $request->user();
        $user->loadMissing(['kycVerification.documents.docType', 'kycVerification.verifiedBy', 'kycVerification.approvedBy']);

        $kyc = $user->kycVerification;

        abort_if(! $kyc, 404);

        return $this->renderPdf($kyc);
    }

    private function renderPdf(KycVerification $kyc): Response
    {
        $pdf = Pdf::loadView('pdf.kyc_verification', [
            'kyc' => $kyc,
            'photoData' => $this->resizedImageDataUri($kyc->selfie_photo_path),
            'docData' => $this->resizedImageDataUri($kyc->id_document_path),
            'signatureData' => $this->resizedImageDataUri($kyc->signature_path, 300),
        ])->setPaper('a4', 'portrait');

        $refNo = 'AGJ-KYC-'.str_pad((string) $kyc->id, 5, '0', STR_PAD_LEFT);

        return $pdf->download("kyc-{$refNo}.pdf");
    }

    /**
     * Downscale a stored public-disk image to a max dimension and return it
     * as a base64 data URI, so DomPDF never has to load the original
     * full-resolution upload into memory.
     */
    private function resizedImageDataUri(?string $path, int $maxDim = 600): ?string
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
