<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use App\Models\PropertyPhoto;
use Filament\Actions;
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
            $data['rental_amount'] = $listing->rental_amount;
        }

        $data['property_photos'] = $property->photos()->pluck('file_ref')->toArray();

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
                'expected_selling_price' => $data['expected_selling_price'] ?? $listing->expected_selling_price,
                'rental_amount' => $data['rental_amount'] ?? $listing->rental_amount,
            ]);
        }

        unset($data['property_photos']);

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
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
