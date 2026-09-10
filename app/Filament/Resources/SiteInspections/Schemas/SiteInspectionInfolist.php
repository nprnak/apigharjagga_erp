<?php

namespace App\Filament\Resources\SiteInspections\Schemas;

use App\Models\SiteInspection;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class SiteInspectionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('property.property_code')
                            ->label('Property')
                            ->placeholder('—'),
                        TextEntry::make('status')
                            ->label('Workflow')
                            ->badge()
                            ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : '—')
                            ->color(fn (?string $state): string => match ($state) {
                                'reviewed' => 'success',
                                'submitted' => 'warning',
                                'draft' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('final_status')
                            ->label('Recommendation')
                            ->badge()
                            ->placeholder('Not set')
                            ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : 'Not set')
                            ->color(fn (?string $state): string => match ($state) {
                                'suitable_for_listing' => 'success',
                                'requires_additional_verification' => 'warning',
                                'not_recommended' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('inspection_date')
                            ->label('Inspected')
                            ->date('d M Y')
                            ->placeholder('—'),
                    ]),

                Wizard::make([
                    Step::make('General Info')
                        ->label('1. General Info')
                        ->description('Property & inspector details')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('property.property_code')->label('Property')->placeholder('—'),
                            TextEntry::make('property_owner_name')->label('Owner')->placeholder('—'),
                            TextEntry::make('contact_number')->label('Contact')->placeholder('—'),
                            TextEntry::make('property_location')->label('Location')->placeholder('—')->columnSpan(2),
                            TextEntry::make('municipality')->label('Municipality')->placeholder('—'),
                            TextEntry::make('ward_no')->label('Ward No.')->placeholder('—'),
                            TextEntry::make('inspection_date')->label('Inspection Date')->date('d M Y')->placeholder('—'),
                            TextEntry::make('inspector.name')->label('Inspector')->placeholder('—'),
                            TextEntry::make('inspector_designation')->label('Designation')->placeholder('—'),
                        ]),

                    Step::make('Land Checklist')
                        ->label('2. Land Checklist')
                        ->description('Annex-D §2')
                        ->icon('heroicon-o-map')
                        ->schema(static::checklistEntries('land_checklist', SiteInspection::LAND_ITEMS)),

                    Step::make('Building Checklist')
                        ->label('3. Building Checklist')
                        ->description('Annex-D §3')
                        ->icon('heroicon-o-building-office-2')
                        ->schema(static::checklistEntries('building_checklist', SiteInspection::BUILDING_ITEMS)),

                    Step::make('Location & Market')
                        ->label('4. Location & Market')
                        ->description('Annex-D §4')
                        ->icon('heroicon-o-currency-dollar')
                        ->columns(2)
                        ->schema([
                            TextEntry::make('distance_from_main_road')
                                ->label('Distance from Main Road')
                                ->placeholder('—'),
                            TextEntry::make('nearby_facilities')
                                ->label('Nearby Facilities')
                                ->placeholder('—'),
                            TextEntry::make('commercial_potential')
                                ->label('Commercial Potential')
                                ->placeholder('—'),
                            TextEntry::make('residential_suitability')
                                ->label('Residential Suitability')
                                ->placeholder('—'),
                            TextEntry::make('future_development_potential')
                                ->label('Future Development Potential')
                                ->placeholder('—')
                                ->columnSpanFull(),
                        ]),

                    Step::make('Photos & Documents')
                        ->label('5. Photos & Docs')
                        ->description('Annex-D §5 & §6')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Section::make('Photo Documentation')
                                ->icon('heroicon-o-photo')
                                ->columns(2)
                                ->schema(static::booleanEntries('photo_checklist', SiteInspection::PHOTO_ITEMS)),
                            Section::make('Documents Verified')
                                ->icon('heroicon-o-document-check')
                                ->columns(2)
                                ->schema(static::booleanEntries('documents_checklist', SiteInspection::DOCUMENT_ITEMS)),
                        ]),

                    Step::make('Observation & Status')
                        ->label('6. Observation & Status')
                        ->description('Annex-D §7 & §8')
                        ->icon('heroicon-o-flag')
                        ->schema([
                            TextEntry::make('observation_notes')
                                ->label("Inspector's Observation")
                                ->placeholder('No observation recorded.')
                                ->columnSpanFull(),
                            TextEntry::make('final_status')
                                ->label('Final Inspection Status')
                                ->badge()
                                ->placeholder('Not set')
                                ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : 'Not set')
                                ->color(fn (?string $state): string => match ($state) {
                                    'suitable_for_listing' => 'success',
                                    'requires_additional_verification' => 'warning',
                                    'not_recommended' => 'danger',
                                    default => 'gray',
                                }),
                        ]),

                    Step::make('Review')
                        ->label('7. Review')
                        ->description('Submission & review status')
                        ->icon('heroicon-o-check-badge')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : '—'),
                            TextEntry::make('submitted_to')
                                ->label('Submitted To')
                                ->placeholder('Not submitted')
                                ->formatStateUsing(fn (?string $state) => $state ? str($state)->headline() : 'Not submitted'),
                            TextEntry::make('reviewer.name')
                                ->label('Reviewed By')
                                ->placeholder('—'),
                            TextEntry::make('submitted_at')
                                ->label('Submitted At')
                                ->dateTime('d M Y, H:i')
                                ->placeholder('—'),
                            TextEntry::make('reviewed_at')
                                ->label('Reviewed At')
                                ->dateTime('d M Y, H:i')
                                ->placeholder('—'),
                            TextEntry::make('review_notes')
                                ->label('Review Notes')
                                ->placeholder('No review notes.')
                                ->columnSpanFull(),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString('view-step')
                    ->columnSpanFull(),
            ]);
    }

    /**
     * One row per Annex-D checklist item: name, Yes/No, remarks.
     */
    protected static function checklistEntries(string $field, array $items): array
    {
        return collect($items)
            ->map(fn (string $label, string $key) => Grid::make(12)
                ->schema([
                    TextEntry::make("{$field}.{$key}.item")
                        ->label('Item')
                        ->state($label)
                        ->columnSpan(5),
                    TextEntry::make("{$field}.{$key}.verified")
                        ->label('Result')
                        ->badge()
                        ->placeholder('Not checked')
                        ->formatStateUsing(fn (mixed $state) => match ($state) {
                            true, 1, '1' => 'Yes',
                            false, 0, '0' => 'No',
                            default => 'Not checked',
                        })
                        ->color(fn (mixed $state): string => match ($state) {
                            true, 1, '1' => 'success',
                            false, 0, '0' => 'danger',
                            default => 'gray',
                        })
                        ->columnSpan(3),
                    TextEntry::make("{$field}.{$key}.remarks")
                        ->label('Remarks')
                        ->placeholder('—')
                        ->columnSpan(4),
                ]))
            ->values()
            ->all();
    }

    /**
     * Photo / document presence as labelled Yes/No chips.
     */
    protected static function booleanEntries(string $field, array $items): array
    {
        return collect($items)
            ->map(fn (string $label, string $key) => IconEntry::make("{$field}.{$key}")
                ->label($label)
                ->boolean()
                ->trueColor('success')
                ->falseColor('gray'))
            ->values()
            ->all();
    }
}
