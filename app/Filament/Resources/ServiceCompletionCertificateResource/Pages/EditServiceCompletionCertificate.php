<?php

namespace App\Filament\Resources\ServiceCompletionCertificateResource\Pages;

use App\Filament\Resources\ServiceCompletionCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceCompletionCertificate extends EditRecord
{
    protected static string $resource = ServiceCompletionCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
