<?php

namespace App\Filament\Resources\PropertyHandoverCertificateResource\Pages;

use App\Filament\Resources\PropertyHandoverCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPropertyHandoverCertificates extends ListRecords
{
    protected static string $resource = PropertyHandoverCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
