<?php

namespace App\Filament\Resources\SiteInspections\Pages;

use App\Filament\Resources\SiteInspections\SiteInspectionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewSiteInspection extends ViewRecord
{
    protected static string $resource = SiteInspectionResource::class;

    public function getTitle(): string|Htmlable
    {
        $code = $this->getRecord()->property?->property_code;

        return $code ? "Site Inspection — {$code}" : 'Site Inspection';
    }

    public function getSubheading(): string|Htmlable|null
    {
        $record = $this->getRecord();
        $status = $record->status ? str($record->status)->headline() : 'Draft';
        $date = $record->inspection_date?->format('d M Y');

        return collect([$status, $date])->filter()->implode(' · ');
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
