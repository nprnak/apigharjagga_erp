<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyProperty extends ViewRecord
{
    protected static string $resource = MyPropertyResource::class;

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
