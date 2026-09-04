<?php

namespace App\Filament\User\Resources\MyValuationRequestResource\Pages;

use App\Filament\User\Resources\MyValuationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyValuationRequest extends ViewRecord
{
    protected static string $resource = MyValuationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToRequests')
                ->label('Back to My Requests')
                ->icon('heroicon-o-arrow-left')
                ->url(MyValuationRequestResource::getUrl('index')),
        ];
    }
}
