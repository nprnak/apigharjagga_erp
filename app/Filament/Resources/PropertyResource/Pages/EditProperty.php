<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Models\PropertyPhoto;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProperty extends EditRecord
{
    protected static string $resource = PropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $property = $this->record;
        $data['property_photos'] = $property->photos()->pluck('file_ref')->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

            foreach ($existingPhotos as $existing) {
                if (! in_array($existing->file_ref, $photos, true)) {
                    $existing->delete();
                }
            }

            foreach ($photos as $index => $photoPath) {
                if (! in_array($photoPath, $existingRefs, true)) {
                    PropertyPhoto::create([
                        'property_id' => $property->property_id,
                        'source_type' => 'listing',
                        'photo_type' => $index === 0 ? 'front' : 'other',
                        'file_ref' => $photoPath,
                        'uploaded_at' => now(),
                    ]);
                }
            }
        }
    }
}
