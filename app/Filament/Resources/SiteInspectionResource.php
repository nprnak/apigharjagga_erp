<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\SiteInspectionResource\Pages;
use App\Models\Property;
use App\Models\SiteInspection;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteInspectionResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = SiteInspection::class;

    protected static ?string $navigationLabel = 'Site Inspections';

    protected static function permissionKey(): string
    {
        return 'inspections';
    }

    /**
     * Creating a record schedules the inspection (assigns property + inspector);
     * editing it is where the field inspector fills in findings and conducts it.
     * Both remain available to holders of the blanket 'inspections.manage'.
     */
    public static function canCreate(): bool
    {
        return static::userHasPermission('inspections.schedule') || static::userHasPermission('inspections.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userHasPermission('inspections.conduct') || static::userHasPermission('inspections.manage');
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-map-pin';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Verification & Inspection';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['property', 'inspector']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inspection')
                ->columns(2)
                ->schema([
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    Select::make('inspector_staff_id')
                        ->label('Inspector')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('inspection_date'),
                    TextInput::make('distance_from_main_road'),
                    Select::make('final_status')
                        ->options([
                            'suitable_for_listing' => 'Suitable for Listing',
                            'requires_additional_verification' => 'Requires Additional Verification',
                            'not_recommended' => 'Not Recommended',
                        ]),
                    Textarea::make('nearby_facilities')->columnSpanFull(),
                    Textarea::make('commercial_potential')->columnSpanFull(),
                    Textarea::make('residential_suitability')->columnSpanFull(),
                    Textarea::make('future_development_potential')->columnSpanFull(),
                    Textarea::make('observation_notes')->columnSpanFull(),
                    Select::make('prepared_by_staff_id')
                        ->label('Prepared By')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('prepared_date'),
                    Select::make('verified_by_staff_id')
                        ->label('Verified By')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('verified_date'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('inspector.full_name')
                    ->label('Inspector')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('final_status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'suitable_for_listing' => 'success',
                        'requires_additional_verification' => 'warning',
                        'not_recommended' => 'danger',
                        default => 'gray',
                    })
                    ->placeholder('Pending'),
                Tables\Columns\TextColumn::make('inspection_date')
                    ->date('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('final_status')
                    ->options([
                        'suitable_for_listing' => 'Suitable for Listing',
                        'requires_additional_verification' => 'Requires Additional Verification',
                        'not_recommended' => 'Not Recommended',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('inspection_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteInspections::route('/'),
            'create' => Pages\CreateSiteInspection::route('/create'),
            'edit' => Pages\EditSiteInspection::route('/{record}/edit'),
        ];
    }
}
