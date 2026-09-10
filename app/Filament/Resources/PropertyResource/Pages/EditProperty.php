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
            PropertyResource::approveAction(),
            PropertyResource::rejectAction(),
            PropertyResource::siteInspectionAction(),
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

        // Hard backstop (on top of the disabled UI controls in the form)
        // against listing/approving a property that hasn't had a completed,
        // reviewed site inspection — see Property::hasCompletedSiteInspection().
        if (! $this->record->hasCompletedSiteInspection()) {
            if (($data['approval_status'] ?? null) === 'approved') {
                $data['approval_status'] = $this->record->approval_status;

                \Filament\Notifications\Notification::make()
                    ->title('Cannot approve this property yet')
                    ->body('It needs a completed & reviewed site inspection first.')
                    ->warning()
                    ->send();
            }

            if (($data['is_listed'] ?? false) === true) {
                $data['is_listed'] = false;

                \Filament\Notifications\Notification::make()
                    ->title('Cannot list this property yet')
                    ->body('It needs a completed & reviewed site inspection first.')
                    ->warning()
                    ->send();
            }
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
