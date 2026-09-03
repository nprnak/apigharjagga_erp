<?php

namespace App\Filament\Resources\SiteInspections\Pages;

use App\Filament\Resources\SiteInspections\SiteInspectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSiteInspection extends ViewRecord
{
    protected static string $resource = SiteInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
