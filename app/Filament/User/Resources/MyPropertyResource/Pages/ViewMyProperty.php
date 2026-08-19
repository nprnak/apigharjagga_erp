<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyProperty extends ViewRecord
{
    protected static string $resource = MyPropertyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
