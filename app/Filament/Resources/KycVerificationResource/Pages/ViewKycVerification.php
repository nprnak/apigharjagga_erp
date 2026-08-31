<?php

namespace App\Filament\Resources\KycVerificationResource\Pages;

use App\Filament\Resources\KycVerificationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewKycVerification extends ViewRecord
{
    protected static string $resource = KycVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('printPdf')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn (): string => route('admin.kyc.pdf', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
