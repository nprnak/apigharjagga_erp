<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyPropertyResource\Pages;
use App\Filament\Support\LocationSelects;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
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
            ->with(['photos', 'address'])
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
            Section::make('Property Details & Specifications')
                ->description('Tell us what you are listing — its type, your ownership capacity, and the structural facts a buyer checks first.')
                ->icon('heroicon-o-home-modern')
                ->aside()
                ->columns(2)
                ->schema(static::propertyDetailsFields()),

            Section::make('Location & Administrative Details')
                ->description('Where the property sits, as recorded by the local government. Buyers filter by these fields first.')
                ->icon('heroicon-o-map')
                ->aside()
                ->columns(2)
                ->schema(static::locationFields()),

            Section::make('Financials & Pricing Expectations')
                ->description('Set the numbers you expect. Fill the selling price for sales, the monthly rent for rentals — or both.')
                ->icon('heroicon-o-banknotes')
                ->aside()
                ->columns(2)
                ->schema(static::financialFields()),

            Section::make('Property Photographs & Media')
                ->description('Listings with photos get far more enquiries. Show the exterior, interior, road access, and surroundings.')
                ->icon('heroicon-o-camera')
                ->aside()
                ->schema(static::mediaFields()),
        ]);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Field>
     */
    public static function propertyDetailsFields(): array
    {
        return [
            TextInput::make('property_code')
                ->label('Property Reference Code')
                ->disabled()
                ->prefixIcon('heroicon-m-hashtag')
                ->placeholder('Auto-generated on submission')
                ->columnSpanFull(),

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
                ->native(false)
                ->searchable()
                ->required(),

            Select::make('ownership_role')
                ->label('Ownership Capacity')
                ->options([
                    'self' => 'Sole Owner (Self)',
                    'family_member' => 'Family Member',
                    'authorized_representative' => 'Authorized Power of Attorney / Representative',
                    'company' => 'Company / Corporate Entity',
                ])
                ->native(false)
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
                ->native(false)
                ->required(),

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
                ])
                ->native(false)
                ->prefixIcon('heroicon-m-globe-alt'),

            TextInput::make('kitta_no')
                ->label('Kitta Number (कित्ता नं.)')
                ->prefixIcon('heroicon-m-map-pin')
                ->maxLength(50),

            TextInput::make('area')
                ->label('Land Area (जग्गाको क्षेत्रफल)')
                ->placeholder('0-4-2-0 or 1500 sq.ft')
                ->hint('Ropani-Aana or sq.ft')
                ->maxLength(100),

            TextInput::make('covered_area')
                ->label('Covered / Built-up Area')
                ->placeholder('2400 sq.ft')
                ->maxLength(100),

            TextInput::make('no_of_floors')
                ->label('Number of Stories / Floors')
                ->numeric()
                ->minValue(0),

            TextInput::make('year_of_construction')
                ->label('Year Built (B.S. / A.D.)')
                ->numeric()
                ->minValue(1950),

            TextInput::make('structure_type')
                ->label('Structure System')
                ->placeholder('RCC Frame / Load Bearing')
                ->maxLength(100),

            TextInput::make('parking')
                ->label('Parking Capacity')
                ->placeholder('2 Cars + 4 Bikes')
                ->prefixIcon('heroicon-m-truck')
                ->maxLength(100),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Field>
     */
    public static function locationFields(): array
    {
        return [
            ...LocationSelects::make(
                province: 'province',
                district: 'district',
                municipality: 'municipality',
                ward: 'ward_no',
                required: true,
            ),

            TextInput::make('tole_locality')
                ->label('Tole / Locality / Landmark')
                ->prefixIcon('heroicon-m-map-pin')
                ->columnSpanFull()
                ->required()
                ->maxLength(150),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Field>
     */
    public static function financialFields(): array
    {
        return [
            TextInput::make('expected_selling_price')
                ->label('Expected Selling Price')
                ->numeric()
                ->prefix('Rs.')
                ->minValue(0)
                ->hint('NPR, negotiable'),

            TextInput::make('rental_amount')
                ->label('Expected Monthly Rental')
                ->numeric()
                ->prefix('Rs.')
                ->minValue(0)
                ->hint('NPR / month'),
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Field>
     */
    public static function mediaFields(): array
    {
        return [
            FileUpload::make('property_photos')
                ->label('Property Photographs (तस्विरहरू)')
                ->multiple()
                ->reorderable()
                ->panelLayout('grid')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                ->maxFiles(12)
                ->maxSize(20480)
                ->disk('public')
                ->directory('properties/photos')
                ->openable()
                ->downloadable()
                ->imageEditor()
                ->helperText('JPG, PNG or WebP up to 20 MB each. Add up to 12 — drag to reorder; the first photo becomes the cover.')
                ->columnSpanFull(),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photos.file_ref')
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(3),

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
