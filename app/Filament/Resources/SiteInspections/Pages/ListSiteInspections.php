<?php

namespace App\Filament\Resources\SiteInspections\Pages;

use App\Filament\Resources\SiteInspections\SiteInspectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteInspections extends ListRecords
{
    protected static string $resource = SiteInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
