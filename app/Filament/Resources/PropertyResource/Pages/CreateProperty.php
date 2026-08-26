<?php

namespace App\Filament\Resources\PropertyResource\Pages;

use App\Filament\Resources\PropertyResource;
use App\Models\PropertyPhoto;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['property_photos']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $property = $this->record;
        $formData = $this->form->getRawState();

        if (! empty($formData['property_photos'])) {
            $photos = is_array($formData['property_photos']) ? $formData['property_photos'] : [$formData['property_photos']];
            foreach ($photos as $index => $photoPath) {
                if ($photoPath) {
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
