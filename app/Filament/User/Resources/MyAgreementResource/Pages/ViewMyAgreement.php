<?php

namespace App\Filament\User\Resources\MyAgreementResource\Pages;

use App\Filament\User\Resources\MyAgreementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyAgreement extends ViewRecord
{
    protected static string $resource = MyAgreementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToAgreements')
                ->label('Back to My Agreements')
                ->icon('heroicon-o-arrow-left')
                ->url(MyAgreementResource::getUrl('index')),
        ];
    }
}
