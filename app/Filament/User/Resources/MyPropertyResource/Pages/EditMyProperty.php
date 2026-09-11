<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use App\Models\DocumentType;
use App\Models\PropertyDocument;
use App\Models\PropertyPhoto;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditMyProperty extends EditRecord
{
    protected static string $resource = MyPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $property = $this->record;

        if ($property->address) {
            $data['province'] = $property->address->province;
            $data['district'] = $property->address->district;
            $data['municipality'] = $property->address->municipality;
            $data['ward_no'] = $property->address->ward_no;
            $data['tole_locality'] = $property->address->tole_locality;
        }

        $listing = $property->listings()->latest()->first();
        if ($listing) {
            $data['purpose_of_listing'] = $listing->purpose_of_listing;
            $data['expected_selling_price'] = $listing->expected_selling_price;
            $data['negotiable'] = $listing->negotiable;
            $data['minimum_acceptable_price'] = $listing->minimum_acceptable_price;
            $data['rental_amount'] = $listing->rental_amount;
            $data['applicant_signature_path'] = $listing->applicant_signature_path;
            $data['declaration_accepted'] = (bool) $listing->applicant_signature_path;
        }

        $data['property_photos'] = $property->photos()->pluck('file_ref')->toArray();
        $data['feature_ids'] = $property->features()->pluck('property_feature_types.feature_id')->toArray();

        $docsByName = $property->documents()->with('docType')->get()->keyBy(fn ($d) => $d->docType?->doc_name);
        foreach (MyPropertyResource::documentFieldMap() as $field => $docName) {
            $data[$field] = $docsByName->get($docName)?->file_ref;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $property = $this->record;

        if ($property->address) {
            $property->address->update([
                'province' => $data['province'] ?? $property->address->province,
                'district' => $data['district'] ?? $property->address->district,
                'municipality' => $data['municipality'] ?? $property->address->municipality,
                'ward_no' => $data['ward_no'] ?? $property->address->ward_no,
                'tole_locality' => $data['tole_locality'] ?? $property->address->tole_locality,
            ]);
        }

        $listing = $property->listings()->latest()->first();
        if ($listing) {
            $listing->update([
                'purpose_of_listing' => $data['purpose_of_listing'] ?? $listing->purpose_of_listing,
                'expected_selling_price' => $data['expected_selling_price'] ?? null,
                'negotiable' => (bool) ($data['negotiable'] ?? false),
                'minimum_acceptable_price' => $data['minimum_acceptable_price'] ?? null,
                'rental_amount' => $data['rental_amount'] ?? null,
                'applicant_signature_path' => $data['applicant_signature_path'] ?? $listing->applicant_signature_path,
                // Resubmitting after a rejection goes back to pending review —
                // the whole point of the reject -> edit -> resubmit loop.
                'listing_status' => 'pending',
                'remarks' => null,
            ]);
        }

        if ($data['ownership_role'] === 'self') {
            $data['owner_full_name'] = null;
            $data['owner_citizenship_no'] = null;
            $data['owner_relation'] = null;
        }

        // This resubmission is what re-enters admin review — matches the
        // mermaid flow's "Rejected -> Provide Feedback -> List Property" loop.
        $data['approval_status'] = 'pending';
        $data['status'] = 'under_verification';

        foreach ([
            'province', 'district', 'municipality', 'ward_no', 'tole_locality',
            'property_photos', 'purpose_of_listing', 'expected_selling_price',
            'negotiable', 'minimum_acceptable_price', 'rental_amount',
            'feature_ids', 'applicant_signature_path', 'declaration_accepted',
            ...array_keys(MyPropertyResource::documentFieldMap()),
        ] as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $property = $this->record;
        $formData = $this->form->getRawState();

        if (isset($formData['property_photos'])) {
            $photos = is_array($formData['property_photos']) ? array_filter($formData['property_photos']) : [];

            $existingPhotos = $property->photos()->get();
            $existingRefs = $existingPhotos->pluck('file_ref')->toArray();

            // Delete removed photos
            foreach ($existingPhotos as $existing) {
                if (! in_array($existing->file_ref, $photos, true)) {
                    $existing->delete();
                }
            }

            // Insert newly added photos
            foreach ($photos as $index => $photoPath) {
                if (! in_array($photoPath, $existingRefs, true)) {
                    PropertyPhoto::create([
                        'property_id' => $property->property_id,
                        'source_type' => 'listing',
                        'source_id' => $property->listings()->latest()->first()?->listing_id,
                        'photo_type' => $index === 0 ? 'front' : 'other',
                        'file_ref' => $photoPath,
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }

        foreach (MyPropertyResource::documentFieldMap() as $field => $docName) {
            $fileRef = $formData[$field] ?? null;
            $docType = DocumentType::firstOrCreate(['doc_name' => $docName], ['category' => 'land']);

            PropertyDocument::updateOrCreate(
                ['property_id' => $property->property_id, 'doc_type_id' => $docType->doc_type_id],
                ['file_ref' => $fileRef, 'status' => $fileRef ? 'submitted' : 'pending', 'updated_at' => now()],
            );
        }

        $property->features()->sync($formData['feature_ids'] ?? []);

        Notification::make()
            ->title('Property Listing Resubmitted')
            ->body('Your changes have been saved and the listing is back under admin review.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
