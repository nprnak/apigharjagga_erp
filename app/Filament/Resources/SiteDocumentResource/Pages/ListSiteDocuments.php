<?php

namespace App\Filament\Resources\SiteDocumentResource\Pages;

use App\Filament\Resources\SiteDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSiteDocuments extends ListRecords
{
    protected static string $resource = SiteDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
