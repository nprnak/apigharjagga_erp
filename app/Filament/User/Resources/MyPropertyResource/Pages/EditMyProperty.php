<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
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

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
