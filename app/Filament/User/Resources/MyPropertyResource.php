<?php

namespace App\Filament\User\Resources;

use App\Filament\Support\LocationSelects;
use App\Filament\User\Resources\MyPropertyResource\Pages;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

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
        $user = Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Owners see what they submitted themselves; Agents additionally
        // see properties belonging to any owner they hold an approved
        // Power of Attorney for (see MyPowerOfAttorneyResource).
        $approvedOwnerClientIds = $user->approvedPoaOwnerClientIds();

        return $query
            ->where(function (Builder $q) use ($user, $approvedOwnerClientIds) {
                $q->where('user_id', $user->id);

                if ($approvedOwnerClientIds !== []) {
                    $q->orWhereIn('owner_client_id', $approvedOwnerClientIds);
                }
            })
            ->with(['photos', 'address'])
            ->orderByDesc('property_id');
    }

    /**
     * Listing a property is an Owner action (see the RBAC matrix's "List
     * Property" row). An Agent gets the same viewing/editing access to
     * properties they hold an approved Power of Attorney for, scoped in
     * getEloquentQuery() above — but not the ability to create a brand new
     * listing on an owner's behalf; the self-service creation wizard below
     * assumes the current user is the owner being registered.
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user?->client_type === 'owner'
            || ($user?->client_type === 'agent' && $user->approvedPoaOwnerClientIds() !== []);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->client_type === 'owner' && $user->kycVerification?->status === 'approved';
    }

    public static function form(Schema $schema): Schema
    {
        // Mirrors the admin PropertyResource workspace layout — an editable
        // main column plus a read-only context sidebar — but exposes only
        // fields the owner is allowed to see/edit. Admin-only controls
        // (approval override, marketplace visibility toggle, owner/managed-by
        // details) are intentionally left out.
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                // ---- Main editable column ---------------------------------
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Property Details & Specifications')
                            ->description('Tell us what you are listing — its type, your ownership capacity, and the structural facts a buyer checks first.')
                            ->icon('heroicon-o-home-modern')
                            ->columns(2)
                            ->schema(static::propertyDetailsFields()),

                        Section::make('Location & Administrative Details')
                            ->description('Where the property sits, as recorded by the local government. Buyers filter by these fields first.')
                            ->icon('heroicon-o-map')
                            ->columns(2)
                            ->collapsible()
                            ->schema(static::locationFields()),

                        Section::make('Financials & Pricing Expectations')
                            ->description('Set the numbers you expect. Fill the selling price for sales, the monthly rent for rentals — or both.')
                            ->icon('heroicon-o-banknotes')
                            ->columns(2)
                            ->collapsible()
                            ->schema(static::financialFields()),
                    ]),

                // ---- Read-only context sidebar ----------------------------
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Section::make('Application Status')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Placeholder::make('approval_status_display')
                                    ->label('Admin Review')
                                    ->content(fn (?Property $record) => static::statusBadge(
                                        $record?->approval_status,
                                        ['approved' => 'success', 'pending' => 'warning', 'rejected' => 'danger'],
                                    )),
                                Placeholder::make('status_display')
                                    ->label('Marketplace Status')
                                    ->content(fn (?Property $record) => static::statusBadge(
                                        $record?->status,
                                        [
                                            'listed' => 'success', 'draft' => 'gray',
                                            'sold' => 'info', 'rented' => 'info', 'leased' => 'info',
                                            'rejected' => 'danger', 'withdrawn' => 'danger',
                                        ],
                                    )),
                            ]),

                        Section::make('Record')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Placeholder::make('property_code_meta')
                                    ->label('Property Code')
                                    ->content(fn (?Property $record) => $record?->property_code ?? '—'),
                                Placeholder::make('created_at')
                                    ->label('Submitted')
                                    ->content(fn (?Property $record) => $record?->created_at?->format('d M Y, H:i') ?? '—'),
                                Placeholder::make('updated_at')
                                    ->label('Last Updated')
                                    ->content(fn (?Property $record) => $record?->updated_at?->format('d M Y, H:i') ?? '—'),
                            ]),
                    ]),

                // ---- Media, full width -------------------------------------
                Section::make('Property Photographs & Media')
                    ->description('Listings with photos get far more enquiries. Show the exterior, interior, road access, and surroundings.')
                    ->icon('heroicon-o-camera')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema(static::mediaFields()),
            ]);
    }

    protected static function statusBadge(?string $state, array $colors): HtmlString
    {
        if (! $state) {
            return new HtmlString('—');
        }

        $palette = [
            'success' => ['#ecfdf5', '#a7f3d0', '#047857'],
            'warning' => ['#fef3c7', '#fde68a', '#92400e'],
            'danger' => ['#ffe4e6', '#fecdd3', '#be123c'],
            'info' => ['#eff6ff', '#bfdbfe', '#1d4ed8'],
            'gray' => ['#f1f5f9', '#e2e8f0', '#475569'],
        ];

        $color = $colors[$state] ?? 'gray';
        [$bg, $border, $text] = $palette[$color] ?? $palette['gray'];
        $label = ucwords(str_replace('_', ' ', $state));

        return new HtmlString(
            '<span style="display:inline-flex;align-items:center;border-radius:9999px;border:1px solid '.$border.
            ';background-color:'.$bg.';padding:0.125rem 0.625rem;font-size:0.75rem;font-weight:600;color:'.$text.'">'.
            e($label).'</span>'
        );
    }

    /**
     * @return array<int, Field>
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
     * @return array<int, Field>
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
     * @return array<int, Field>
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
     * @return array<int, Field>
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
