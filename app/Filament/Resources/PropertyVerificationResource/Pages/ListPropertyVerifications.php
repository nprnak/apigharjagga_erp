<?php

namespace App\Filament\Resources\PropertyVerificationResource\Pages;

use App\Filament\Resources\PropertyVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyVerifications extends ListRecords
{
    protected static string $resource = PropertyVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
