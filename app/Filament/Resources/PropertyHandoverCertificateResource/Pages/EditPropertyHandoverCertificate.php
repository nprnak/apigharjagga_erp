<?php

namespace App\Filament\Resources\PropertyHandoverCertificateResource\Pages;

use App\Filament\Resources\PropertyHandoverCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPropertyHandoverCertificate extends EditRecord
{
    protected static string $resource = PropertyHandoverCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
