<?php

namespace App\Filament\User\Resources\MyPaymentResource\Pages;

use App\Filament\User\Resources\MyPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyPayment extends ViewRecord
{
    protected static string $resource = MyPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToPayments')
                ->label('Back to My Payments')
                ->icon('heroicon-o-arrow-left')
                ->url(MyPaymentResource::getUrl('index')),
        ];
    }
}
