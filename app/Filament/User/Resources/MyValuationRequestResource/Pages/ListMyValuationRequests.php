<?php

namespace App\Filament\User\Resources\MyValuationRequestResource\Pages;

use App\Filament\User\Resources\MyValuationRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyValuationRequests extends ListRecords
{
    protected static string $resource = MyValuationRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
