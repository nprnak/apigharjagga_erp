<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PropertyHandoverCertificateResource\Pages;
use App\Models\Client;
use App\Models\PropertyHandoverCertificate;
use App\Models\Property;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PropertyHandoverCertificateResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PropertyHandoverCertificate::class;

    protected static ?string $navigationLabel = 'Property Handovers';

    protected static function permissionKey(): string
    {
        return 'handovers';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-key';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Service Delivery';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['ownerClient', 'property', 'companyRep']);
    }

    public static function form(Schema $schema): Schema
    {
        $statusOptions = ['received' => 'Received', 'pending' => 'Pending'];

        return $schema->components([
            Section::make('Handover Details')
                ->columns(2)
                ->schema([
                    TextInput::make('certificate_no')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'HO-'.Str::upper(Str::random(8))),
                    DatePicker::make('handover_date'),
                    Select::make('owner_client_id')
                        ->label('Owner')
                        ->options(fn () => Client::pluck('full_name', 'client_id'))
                        ->required()
                        ->searchable(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    Select::make('company_rep_staff_id')
                        ->label('Company Representative')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    TextInput::make('place'),
                    Select::make('purpose')
                        ->options([
                            'property_listing_service' => 'Property Listing Service',
                            'marketing_promotion' => 'Marketing Promotion',
                            'valuation_process' => 'Valuation Process',
                            'sale_purchase_facilitation' => 'Sale/Purchase Facilitation',
                            'lease_rent_management' => 'Lease/Rent Management',
                            'other' => 'Other',
                        ]),
                    Select::make('possession_status')
                        ->options(['handed_over' => 'Handed Over', 'pending' => 'Pending'])
                        ->required(),
                ]),
            Section::make('Condition Checklist')
                ->columns(3)
                ->collapsible()
                ->schema([
                    Select::make('keys_status')->options($statusOptions),
                    Select::make('ownership_docs_status')->options($statusOptions),
                    Select::make('tax_docs_status')->options($statusOptions),
                    Select::make('utility_docs_status')->options($statusOptions),
                    Select::make('land_boundary_condition')
                        ->options(['clear' => 'Clear', 'not_clear' => 'Not Clear']),
                    Select::make('building_structure_condition')
                        ->options(['good' => 'Good', 'repair_required' => 'Repair Required']),
                    Select::make('electrical_condition')
                        ->options(['functional' => 'Functional', 'not_functional' => 'Not Functional']),
                    Select::make('water_supply_condition')
                        ->options(['available' => 'Available', 'not_available' => 'Not Available']),
                    Select::make('sanitation_condition')
                        ->options(['available' => 'Available', 'not_available' => 'Not Available']),
                    Select::make('furniture_equipment_status')
                        ->options(['available' => 'Available', 'none' => 'None']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certificate_no')
                    ->label('Certificate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ownerClient.full_name')
                    ->label('Owner'),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('possession_status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'handed_over' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('handover_date')
                    ->date('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('possession_status')
                    ->options(['handed_over' => 'Handed Over', 'pending' => 'Pending']),
            ])
            ->actions([
                Actions\Action::make('markHandedOver')
                    ->label('Mark Handed Over')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (PropertyHandoverCertificate $record) => static::userHasPermission('handovers.manage') && $record->possession_status !== 'handed_over')
                    ->requiresConfirmation()
                    ->action(function (PropertyHandoverCertificate $record): void {
                        $record->update(['possession_status' => 'handed_over']);
                        Notification::make()->title('Property marked as handed over')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('handover_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPropertyHandoverCertificates::route('/'),
            'create' => Pages\CreatePropertyHandoverCertificate::route('/create'),
            'edit' => Pages\EditPropertyHandoverCertificate::route('/{record}/edit'),
        ];
    }
}
