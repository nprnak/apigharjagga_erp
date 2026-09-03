<?php

namespace App\Filament\Resources\SiteInspections\Pages;

use App\Filament\Resources\SiteInspections\Schemas\SiteInspectionForm;
use App\Filament\Resources\SiteInspections\SiteInspectionResource;
use App\Models\SiteInspection;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\Url;

class CreateSiteInspection extends CreateRecord
{
    protected static string $resource = SiteInspectionResource::class;

    /**
     * Kept in the query string so a "Start Site Inspection" link from a
     * Property page survives Livewire requests (and the wizard's own `step`
     * query param). Bound from `?property_id=`.
     */
    #[Url(as: 'property_id')]
    public ?string $propertyId = null;

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $this->form->fill($this->prefillFromLinkedProperty());

        $this->callHook('afterFill');
    }

    /**
     * When arriving from a Property page (`?property_id=`), pre-select that
     * property and copy owner / contact / address into the General Info step.
     *
     * @return array<string, mixed>
     */
    protected function prefillFromLinkedProperty(): array
    {
        $propertyId = $this->propertyId ?: request()->query('property_id');

        if (blank($propertyId)) {
            return [];
        }

        $this->propertyId = (string) $propertyId;

        return array_merge(
            ['property_id' => $this->propertyId],
            SiteInspectionForm::prefillDataFromProperty($this->propertyId),
        );
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['inspector_user_id'] = auth()->id();
        $data['status'] = SiteInspection::STATUS_DRAFT;

        if (blank($data['property_id'] ?? null) && filled($this->propertyId)) {
            $data['property_id'] = $this->propertyId;
        }

        foreach (SiteInspection::defaultChecklists() as $field => $default) {
            $data[$field] = array_replace($default, $data[$field] ?? []);
        }

        return $data;
    }
}
