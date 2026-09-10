<?php

namespace App\Filament\Resources\SiteInspections\Schemas;

use App\Models\Property;
use App\Models\SiteInspection;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class SiteInspectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('General Info')
                        ->label('1. General Info')
                        ->description('Property & inspector details')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->columns(3)
                        ->schema([
                            Select::make('property_id')
                                ->label('Property')
                                ->relationship('property', 'property_code')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->disabledOn('edit')
                                ->default(fn () => request()->query('property_id'))
                                ->live()
                                ->afterStateUpdated(fn (mixed $state, callable $set) => static::fillFromProperty($state, $set))
                                ->columnSpan(1),
                            TextInput::make('property_owner_name')
                                ->label('Property Owner Name')
                                ->maxLength(150),
                            TextInput::make('contact_number')
                                ->label('Contact Number')
                                ->tel()
                                ->maxLength(30),
                            TextInput::make('property_location')
                                ->label('Property Location')
                                ->maxLength(255)
                                ->columnSpan(2),
                            TextInput::make('municipality')
                                ->label('Municipality')
                                ->maxLength(100),
                            TextInput::make('ward_no')
                                ->label('Ward No.')
                                ->maxLength(20),
                            DatePicker::make('inspection_date')
                                ->label('Inspection Date')
                                ->default(now())
                                ->native(false),
                            Placeholder::make('inspector_name')
                                ->label('Inspector Name')
                                ->content(fn (?SiteInspection $record) => $record?->inspector?->name ?? auth()->user()?->name ?? '—'),
                            TextInput::make('inspector_designation')
                                ->label('Designation')
                                ->maxLength(100),
                        ]),

                    Step::make('Land Checklist')
                        ->label('2. Land Checklist')
                        ->description('9 fixed items — Annex-D §2')
                        ->icon('heroicon-o-map')
                        ->schema(static::checklistRows('land_checklist', SiteInspection::LAND_ITEMS)),

                    Step::make('Building Checklist')
                        ->label('3. Building Checklist')
                        ->description('10 fixed items — Annex-D §3')
                        ->icon('heroicon-o-building-office-2')
                        ->schema(static::checklistRows('building_checklist', SiteInspection::BUILDING_ITEMS)),

                    Step::make('Location & Market')
                        ->label('4. Location & Market')
                        ->description('Annex-D §4')
                        ->icon('heroicon-o-currency-dollar')
                        ->columns(2)
                        ->schema([
                            TextInput::make('distance_from_main_road')
                                ->label('Distance from Main Road')
                                ->maxLength(100),
                            Textarea::make('nearby_facilities')
                                ->label('Nearby Facilities')
                                ->rows(2),
                            Textarea::make('commercial_potential')
                                ->label('Commercial Potential')
                                ->rows(2),
                            Textarea::make('residential_suitability')
                                ->label('Residential Suitability')
                                ->rows(2),
                            Textarea::make('future_development_potential')
                                ->label('Future Development Potential')
                                ->rows(2)
                                ->columnSpanFull(),
                        ]),

                    Step::make('Photos & Documents')
                        ->label('5. Photos & Docs')
                        ->description('Annex-D §5 & §6')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Section::make('Photo Documentation Checklist')
                                ->icon('heroicon-o-photo')
                                ->columns(4)
                                ->schema(
                                    collect(SiteInspection::PHOTO_ITEMS)
                                        ->map(fn (string $label, string $key) => Toggle::make("photo_checklist.$key")->label($label))
                                        ->values()
                                        ->all()
                                ),
                            Section::make('Documents Verified')
                                ->icon('heroicon-o-document-check')
                                ->columns(3)
                                ->schema(
                                    collect(SiteInspection::DOCUMENT_ITEMS)
                                        ->map(fn (string $label, string $key) => Toggle::make("documents_checklist.$key")->label($label))
                                        ->values()
                                        ->all()
                                ),
                        ]),

                    Step::make('Observation & Status')
                        ->label('6. Observation & Status')
                        ->description('Annex-D §7 & §8')
                        ->icon('heroicon-o-flag')
                        ->schema([
                            Textarea::make('observation_notes')
                                ->label("Inspector's Observation")
                                ->rows(4),
                            Radio::make('final_status')
                                ->label('Final Inspection Status')
                                ->options([
                                    'suitable_for_listing' => 'Property Suitable for Listing',
                                    'requires_additional_verification' => 'Requires Additional Verification',
                                    'not_recommended' => 'Not Recommended',
                                ]),
                        ]),

                    Step::make('Review')
                        ->label('7. Review')
                        ->description('Submission & review status')
                        ->icon('heroicon-o-check-badge')
                        ->columns(3)
                        ->visible(fn (?SiteInspection $record) => $record && $record->status !== SiteInspection::STATUS_DRAFT)
                        ->schema([
                            Placeholder::make('status_display')
                                ->label('Status')
                                ->content(fn (?SiteInspection $record) => $record ? ucfirst($record->status) : '—'),
                            Placeholder::make('submitted_to_display')
                                ->label('Submitted To')
                                ->content(fn (?SiteInspection $record) => $record?->submitted_to ? str($record->submitted_to)->headline() : '—'),
                            Placeholder::make('reviewer_display')
                                ->label('Reviewed By')
                                ->content(fn (?SiteInspection $record) => $record?->reviewer?->name ?? '—'),
                            Textarea::make('review_notes')
                                ->label('Review Notes')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Pull owner/contact/address fields from a Property for pre-filling a new
     * Site Inspection form (used when linking from a Property page).
     *
     * @return array<string, mixed>
     */
    public static function prefillDataFromProperty(int|string|null $propertyId): array
    {
        $data = [];

        static::fillFromProperty($propertyId, function (string $key, mixed $value) use (&$data) {
            $data[$key] = $value;
        });

        return $data;
    }

    /**
     * When a Property is picked, pull whatever we already know about it
     * (owner, contact, address) and use it to fill the rest of the
     * "General Info" step — the inspector only needs to correct/confirm
     * it on-site rather than retype everything from scratch.
     */
    public static function fillFromProperty(int|string|null $state, callable $set): void
    {
        if (blank($state)) {
            return;
        }

        $property = Property::with(['owner', 'address'])->find($state);

        if (! $property) {
            return;
        }

        $owner = $property->owner;
        $address = $property->address;

        if ($owner) {
            $set('property_owner_name', $owner->full_name);
            $set('contact_number', $owner->mobile_no ?? $owner->alt_contact_no ?? $owner->telephone_no);
        }

        if ($address) {
            $set('municipality', $address->municipality);
            $set('ward_no', $address->ward_no);
            $set('property_location', collect([
                $address->tole_locality,
                $address->municipality ? "{$address->municipality} - {$address->ward_no}" : null,
                $address->district,
                $address->province,
            ])->filter()->implode(', ') ?: $address->full_address_text);
        }
    }

    /**
     * Builds one Grid row per checklist item: a read-only label, a Yes/No
     * radio bound to `{field}.{key}.verified`, and a remarks input bound to
     * `{field}.{key}.remarks`.
     */
    protected static function checklistRows(string $field, array $items): array
    {
        return collect($items)
            ->map(fn (string $label, string $key) => Grid::make(4)
                ->schema([
                    Placeholder::make("{$field}_{$key}_label")
                        ->label(false)
                        ->content($label)
                        ->columnSpan(2),
                    Radio::make("{$field}.{$key}.verified")
                        ->label(false)
                        ->boolean()
                        ->inline()
                        ->columnSpan(1),
                    TextInput::make("{$field}.{$key}.remarks")
                        ->label(false)
                        ->placeholder('Remarks')
                        ->columnSpan(1),
                ]))
            ->values()
            ->all();
    }
}
