<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyPropertyResource\Pages;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyPropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationLabel = 'My Properties';

    protected static ?string $modelLabel = 'Property Listing';

    protected static ?string $pluralModelLabel = 'My Properties';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'property_code';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-home-modern';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $userId = Auth::id();
        if (! $userId) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('user_id', $userId)
            ->orderByDesc('property_id');
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        $isKycApproved = Auth::user()?->kycVerification?->status === 'approved';

        return $schema->components([
            Section::make('1. Property Details & Specifications')
                ->description('Specify property type, ownership, and structural dimensions')
                ->columns(2)
                ->schema([
                    TextInput::make('property_code')
                        ->label('Property Reference Code')
                        ->disabled()
                        ->placeholder('Auto-generated upon submission'),

                    Select::make('property_type')
                        ->label('Property Category')
                        ->options([
                            'land' => 'Land (जग्गा)',
                            'house' => 'House (घर)',
                            'apartment' => 'Apartment (अपार्टमेन्ट)',
                            'commercial_building' => 'Commercial Building',
                            'office_space' => 'Office Space',
                            'industrial_property' => 'Industrial Property',
                            'agricultural_land' => 'Agricultural Land',
                            'other' => 'Other',
                        ])
                        ->required(),

                    Select::make('ownership_role')
                        ->label('Ownership Capacity')
                        ->options([
                            'self' => 'Sole Owner (Self)',
                            'family_member' => 'Family Member',
                            'authorized_representative' => 'Authorized Power of Attorney / Representative',
                            'company' => 'Company / Corporate Entity',
                        ])
                        ->required(),

                    Select::make('purpose_of_listing')
                        ->label('Listing Intent')
                        ->options([
                            'sale' => 'For Sale (बिक्री)',
                            'rent' => 'For Rent (भाडा)',
                            'lease' => 'Long-Term Lease',
                            'investment' => 'Joint Investment',
                        ])
                        ->default('sale')
                        ->required(),

                    TextInput::make('kitta_no')
                        ->label('Kitta Number (कित्ता नं.)')
                        ->maxLength(50),

                    TextInput::make('area')
                        ->label('Land Area (जग्गाको क्षेत्रफल)')
                        ->placeholder('e.g. 0-4-2-0 or 1500 sq.ft')
                        ->maxLength(100),

                    TextInput::make('covered_area')
                        ->label('Covered / Built-up Area')
                        ->placeholder('e.g. 2400 sq.ft')
                        ->maxLength(100),

                    TextInput::make('no_of_floors')
                        ->label('Number of Stories / Floors')
                        ->numeric()
                        ->minValue(0),

                    TextInput::make('year_of_construction')
                        ->label('Year Built (B.S. / A.D.)')
                        ->numeric()
                        ->minValue(1950),

                    Select::make('facing_direction')
                        ->label('Facing Orientation (दिशा)')
                        ->options([
                            'East' => 'East (पूर्व)',
                            'West' => 'West (पश्चिम)',
                            'North' => 'North (उत्तर)',
                            'South' => 'South (दक्षिण)',
                            'North-East' => 'North-East (ईशान)',
                            'South-East' => 'South-East (आग्नेय)',
                            'North-West' => 'North-West (वायव्य)',
                            'South-West' => 'South-West (नैऋत्य)',
                        ]),

                    TextInput::make('structure_type')
                        ->label('Structure System')
                        ->placeholder('e.g. RCC Frame / Load Bearing')
                        ->maxLength(100),

                    TextInput::make('parking')
                        ->label('Parking Capacity')
                        ->placeholder('e.g. 2 Cars + 4 Bikes')
                        ->maxLength(100),
                ]),

            Section::make('2. Location & Administrative Details')
                ->description('Location details according to local government administration')
                ->columns(2)
                ->schema([
                    TextInput::make('province')
                        ->label('Province (प्रदेश)')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('district')
                        ->label('District (जिल्ला)')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('municipality')
                        ->label('Municipality / Rural Municipality (गा.पा. / न.पा.)')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('ward_no')
                        ->label('Ward No. (वडा नं.)')
                        ->required()
                        ->maxLength(10),

                    TextInput::make('tole_locality')
                        ->label('Tole / Locality / Landmark')
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(150),
                ]),

            Section::make('3. Financials & Pricing Expectations')
                ->description('Expected pricing for sale or monthly rent')
                ->columns(2)
                ->schema([
                    TextInput::make('expected_selling_price')
                        ->label('Expected Selling Price (NPR)')
                        ->numeric()
                        ->prefix('Rs.')
                        ->minValue(0),

                    TextInput::make('rental_amount')
                        ->label('Expected Monthly Rental (NPR)')
                        ->numeric()
                        ->prefix('Rs.')
                        ->minValue(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property_code')
                    ->label('Property Code')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('property_type')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),

                Tables\Columns\TextColumn::make('address.municipality')
                    ->label('Location')
                    ->formatStateUsing(fn ($state, $record) => $state ? ($state.', '.$record->address?->district) : '—'),

                Tables\Columns\TextColumn::make('area')
                    ->label('Area')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Admin Review')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Marketplace Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'listed' => 'success',
                        'draft' => 'gray',
                        'sold', 'rented', 'leased' => 'info',
                        'rejected', 'withdrawn' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Review Status')
                    ->options([
                        'pending' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('property_type')
                    ->label('Property Type')
                    ->options([
                        'land' => 'Land',
                        'house' => 'House',
                        'apartment' => 'Apartment',
                        'commercial_building' => 'Commercial Building',
                        'office_space' => 'Office Space',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make()
                    ->hidden(fn (Property $record) => in_array($record->status, ['sold', 'rented', 'leased'])),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyProperties::route('/'),
            'create' => Pages\CreateMyProperty::route('/create'),
            'view' => Pages\ViewMyProperty::route('/{record}'),
            'edit' => Pages\EditMyProperty::route('/{record}/edit'),
        ];
    }
}
