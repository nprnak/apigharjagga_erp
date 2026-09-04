<?php

namespace App\Filament\Resources\ServiceCompletionCertificateResource\Pages;

use App\Filament\Resources\ServiceCompletionCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListServiceCompletionCertificates extends ListRecords
{
    protected static string $resource = ServiceCompletionCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
