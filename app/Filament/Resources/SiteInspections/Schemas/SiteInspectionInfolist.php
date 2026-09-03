<?php

namespace App\Filament\Resources\SiteInspections\Schemas;

use App\Models\SiteInspection;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteInspectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Inspection Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('property.property_code')->label('Property'),
                        TextEntry::make('property_owner_name')->label('Owner'),
                        TextEntry::make('contact_number')->label('Contact'),
                        TextEntry::make('property_location')->label('Location'),
                        TextEntry::make('municipality')->label('Municipality'),
                        TextEntry::make('ward_no')->label('Ward No.'),
                        TextEntry::make('inspection_date')->date('d M Y'),
                        TextEntry::make('inspector.name')->label('Inspector'),
                        TextEntry::make('inspector_designation')->label('Designation'),
                    ]),

                Section::make('Land Site Inspection Checklist')
                    ->collapsible()
                    ->schema(static::checklistEntries('land_checklist', SiteInspection::LAND_ITEMS)),

                Section::make('Building / House Inspection Checklist')
                    ->collapsible()
                    ->schema(static::checklistEntries('building_checklist', SiteInspection::BUILDING_ITEMS)),

                Section::make('Location & Market Assessment')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('distance_from_main_road'),
                        TextEntry::make('nearby_facilities'),
                        TextEntry::make('commercial_potential'),
                        TextEntry::make('residential_suitability'),
                        TextEntry::make('future_development_potential')->columnSpanFull(),
                    ]),

                Section::make("Inspector's Observation")
                    ->schema([
                        TextEntry::make('observation_notes')->label(false),
                    ]),

                Section::make('Final Inspection Status')
                    ->schema([
                        TextEntry::make('final_status')
                            ->label(false)
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : '—'),
                    ]),

                Section::make('Review & Workflow')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('status')->badge(),
                        TextEntry::make('submitted_to')
                            ->label('Submitted To')
                            ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : '—'),
                        TextEntry::make('reviewer.name')->label('Reviewed By')->placeholder('—'),
                        TextEntry::make('review_notes')->columnSpanFull()->placeholder('—'),
                    ]),
            ]);
    }

    protected static function checklistEntries(string $field, array $items): array
    {
        return collect($items)
            ->map(fn (string $label, string $key) => Grid::make(3)
                ->schema([
                    TextEntry::make("{$field}.{$key}.label")
                        ->label(false)
                        ->state($label)
                        ->columnSpan(2),
                    IconEntry::make("{$field}.{$key}.verified")
                        ->label(false)
                        ->boolean()
                        ->columnSpan(1),
                ]))
            ->values()
            ->all();
    }
}
