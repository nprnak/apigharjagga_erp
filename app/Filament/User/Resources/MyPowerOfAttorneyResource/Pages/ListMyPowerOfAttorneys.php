<?php

namespace App\Filament\User\Resources\MyPowerOfAttorneyResource\Pages;

use App\Filament\User\Resources\MyPowerOfAttorneyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyPowerOfAttorneys extends ListRecords
{
    protected static string $resource = MyPowerOfAttorneyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
