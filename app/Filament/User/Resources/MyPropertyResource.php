<?php

namespace App\Filament\User\Resources;

use App\Filament\Support\LocationSelects;
use App\Filament\User\Resources\MyPropertyResource\Pages;
use App\Models\Property;
use App\Models\PropertyFeatureType;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * Statuses that lock the record against further edits — only a fresh
     * rejection can be resubmitted; everything else (pending/approved) is
     * frozen while under or past review, same convention as KYC.
     */
    public const LOCKED_STATUSES = ['pending', 'approved'];

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
            ->with(['photos', 'address', 'documents.docType', 'features', 'listings'])
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

        if (! $user?->hasApprovedKyc()) {
            return false;
        }

        return $user->client_type === 'owner'
            || ($user->client_type === 'agent' && $user->approvedPoaOwnerClientIds() !== []);
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->client_type === 'owner' && $user->hasApprovedKyc();
    }

    /**
     * Only a fresh rejection (or, in practice, never for this record) can
     * be resubmitted — once pending or approved, the listing is frozen,
     * matching the mermaid flow's single admin-review gate.
     */
    public static function canEdit(Model $record): bool
    {
        return ! in_array($record->approval_status, self::LOCKED_STATUSES, true);
    }

    /**
     * Annex A §1 Applicant Details — pulled straight from the owner's own
     * approved KYC record, never re-collected here.
     *
     * @return array<int, Component>
     */
    public static function applicantDetailsFields(): array
    {
        $kyc = Auth::user()?->kycVerification;

        $row = fn (string $label, string $np, ?string $value) => new HtmlString(
            '<div style="display:flex;justify-content:space-between;border-bottom:1px dashed #E2E7EA;padding:4px 0;font-size:0.8rem;">'
            .'<span style="color:#5C6B76;">'.e($label).' <span style="display:block;font-size:0.7rem;">'.e($np).'</span></span>'
            .'<span style="font-weight:600;">'.e($value ?: 'Not provided').'</span>'
            .'</div>',
        );

        return [
            Placeholder::make('applicant_summary')
                ->label('')
                ->content(new HtmlString(
                    $row('Full Name', 'पूरा नाम', $kyc?->full_name)->toHtml()
                    .$row('Citizenship No.', 'नागरिकता नं.', $kyc?->citizenship_no)->toHtml()
                    .$row('Mobile No.', 'मोबाइल नम्बर', $kyc?->mobile_no)->toHtml()
                    .$row('Email', 'इमेल', $kyc?->email)->toHtml()
                    .$row('Permanent Address', 'स्थायी ठेगाना', collect([$kyc?->permanent_tole, $kyc?->permanent_municipality, $kyc?->permanent_district, $kyc?->permanent_province])->filter()->implode(', ') ?: null)->toHtml(),
                )),
        ];
    }

    /**
     * Annex A §2 Property Owner Details.
     *
     * @return array<int, Component>
     */
    public static function ownerDetailsFields(): array
    {
        return [
            Select::make('ownership_role')
                ->label('Ownership Capacity (स्वामित्वको हैसियत)')
                ->options([
                    'self' => 'Sole Owner — I am the owner (म आफैं धनी हुँ)',
                    'family_member' => 'Family Member',
                    'authorized_representative' => 'Authorized Power of Attorney / Representative',
                    'company' => 'Company / Corporate Entity',
                ])
                ->native(false)
                ->live()
                ->required()
                ->columnSpanFull(),
            TextInput::make('owner_full_name')
                ->label("Owner's Full Name (धनीको पूरा नाम)")
                ->maxLength(150)
                ->required(fn (Get $get) => $get('ownership_role') !== 'self')
                ->visible(fn (Get $get) => $get('ownership_role') !== 'self'),
            TextInput::make('owner_citizenship_no')
                ->label("Owner's Citizenship No. (धनीको नागरिकता नं.)")
                ->maxLength(50)
                ->visible(fn (Get $get) => $get('ownership_role') !== 'self'),
            TextInput::make('owner_relation')
                ->label('Relationship / Basis of Authority (सम्बन्ध)')
                ->placeholder('e.g. Son, Registered Power of Attorney, Managing Director')
                ->maxLength(100)
                ->visible(fn (Get $get) => $get('ownership_role') !== 'self'),
        ];
    }

    /**
     * Annex A §3 Property Details — property type, Address of Property,
     * Land Information, and (conditionally) Building Details.
     *
     * @return array<int, Component>
     */
    public static function propertyDetailsFields(): array
    {
        return [
            Select::make('property_type')
                ->label('Property Category (सम्पत्तिको किसिम)')
                ->options([
                    'land' => 'Land (जग्गा)',
                    'house' => 'House (घर)',
                    'apartment' => 'Apartment (अपार्टमेन्ट)',
                    'commercial_building' => 'Commercial Building',
                    'office_space' => 'Office Space',
                    'industrial_property' => 'Industrial Property',
                    'agricultural_land' => 'Agricultural Land (कृषि जग्गा)',
                    'other' => 'Other',
                ])
                ->native(false)
                ->searchable()
                ->live()
                ->required()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, Component>
     */
    public static function addressFields(): array
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
                ->label('Tole / Locality / Landmark (टोल/स्थान)')
                ->prefixIcon('heroicon-m-map-pin')
                ->columnSpanFull()
                ->required()
                ->maxLength(150),
        ];
    }

    /**
     * @return array<int, Component>
     */
    public static function landInformationFields(): array
    {
        return [
            TextInput::make('kitta_no')
                ->label('Kitta Number (कित्ता नं.)')
                ->maxLength(50),
            TextInput::make('area')
                ->label('Land Area (जग्गाको क्षेत्रफल)')
                ->placeholder('0-4-2-0 or 1500 sq.ft')
                ->hint('Ropani-Aana-Paisa-Daam or sq.ft')
                ->required()
                ->maxLength(100),
            TextInput::make('map_sheet_no')
                ->label('Map Sheet No. (नक्सा पाना नं.)')
                ->maxLength(50),
            Select::make('ownership_type')
                ->label('Ownership Type (स्वामित्व प्रकार)')
                ->options(['private' => 'Private', 'joint' => 'Joint', 'other' => 'Other'])
                ->native(false),
            TextInput::make('ownership_certificate_no')
                ->label('Ownership Certificate No. (लालपुर्जा नं.)')
                ->maxLength(50),
            Select::make('road_access')
                ->label('Road Access (सडक पहुँच)')
                ->options(['yes' => 'Yes', 'no' => 'No'])
                ->native(false),
            TextInput::make('road_width')
                ->label('Road Width (सडकको चौडाई)')
                ->placeholder('e.g. 20 ft')
                ->maxLength(50),
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
                ->native(false),
        ];
    }

    /**
     * Annex A §3 continued — Building Details, only relevant when the
     * property type carries a structure at all.
     *
     * @return array<int, Component>
     */
    public static function buildingDetailsFields(): array
    {
        return [
            TextInput::make('year_of_construction')
                ->label('Year Built, B.S. / A.D. (निर्माण वर्ष)')
                ->numeric()
                ->minValue(1950),
            TextInput::make('no_of_floors')
                ->label('Number of Floors (तल्ला संख्या)')
                ->numeric()
                ->minValue(0),
            TextInput::make('covered_area')
                ->label('Covered / Built-up Area (ओगटेको क्षेत्रफल)')
                ->placeholder('2400 sq.ft')
                ->maxLength(100),
            TextInput::make('structure_type')
                ->label('Structure System (संरचना प्रकार)')
                ->placeholder('RCC Frame / Load Bearing / Steel')
                ->maxLength(100),
            TextInput::make('roof_type')
                ->label('Roof Type (छानाको प्रकार)')
                ->maxLength(50),
            TextInput::make('parking')
                ->label('Parking Capacity (पार्किङ)')
                ->placeholder('2 Cars + 4 Bikes')
                ->maxLength(100),
            Select::make('water_supply')
                ->label('Water Supply (पानीको आपूर्ति)')
                ->options(['municipal' => 'Municipal', 'well' => 'Well/Borehole', 'both' => 'Both', 'none' => 'None'])
                ->native(false),
            Select::make('electricity')
                ->label('Electricity (बिजुली)')
                ->options(['available' => 'Available', 'not_available' => 'Not Available'])
                ->native(false),
            Select::make('internet')
                ->label('Internet (इन्टरनेट)')
                ->options(['available' => 'Available', 'not_available' => 'Not Available'])
                ->native(false),
            Select::make('drainage')
                ->label('Drainage / Sewerage (ढल निकास)')
                ->options(['available' => 'Available', 'not_available' => 'Not Available'])
                ->native(false),
            TextInput::make('building_permit_no')
                ->label('Building Permit / Naksa Pass No. (नक्सा पास नं.)')
                ->maxLength(50),
            Select::make('current_building_condition')
                ->label('Current Condition (हालको अवस्था)')
                ->options(['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor'])
                ->native(false),
        ];
    }

    /**
     * Annex A §4 Purpose of Listing.
     *
     * @return array<int, Component>
     */
    public static function purposeFields(): array
    {
        return [
            Select::make('purpose_of_listing')
                ->label('Purpose of Listing (सूचीकरणको उद्देश्य)')
                ->options([
                    'sale' => 'For Sale (बिक्री)',
                    'rent' => 'For Rent (भाडा)',
                    'lease' => 'Long-Term Lease (लिज)',
                    'exchange' => 'Exchange (साटासाट)',
                    'investment' => 'Joint Investment (लगानी)',
                    'other' => 'Other',
                ])
                ->default('sale')
                ->native(false)
                ->live()
                ->required()
                ->columnSpanFull(),
        ];
    }

    /**
     * Annex A §5 Expected Price.
     *
     * @return array<int, Component>
     */
    public static function priceFields(): array
    {
        return [
            TextInput::make('expected_selling_price')
                ->label('Expected Selling Price (अपेक्षित मूल्य)')
                ->numeric()
                ->prefix('Rs.')
                ->minValue(0)
                ->visible(fn (Get $get) => in_array($get('purpose_of_listing'), ['sale', 'exchange', 'investment', 'other'], true)),
            Toggle::make('negotiable')
                ->label('Negotiable (मोलमोलाइ हुने)')
                ->inline(false)
                ->visible(fn (Get $get) => in_array($get('purpose_of_listing'), ['sale', 'exchange', 'investment', 'other'], true)),
            TextInput::make('minimum_acceptable_price')
                ->label('Minimum Acceptable Price (न्यूनतम स्वीकार्य मूल्य)')
                ->numeric()
                ->prefix('Rs.')
                ->minValue(0)
                ->visible(fn (Get $get) => in_array($get('purpose_of_listing'), ['sale', 'exchange', 'investment', 'other'], true)),
            TextInput::make('rental_amount')
                ->label('Expected Monthly Rent (मासिक भाडा)')
                ->numeric()
                ->prefix('Rs.')
                ->minValue(0)
                ->visible(fn (Get $get) => in_array($get('purpose_of_listing'), ['rent', 'lease'], true)),
        ];
    }

    /**
     * Annex A §6 Property Documents Submitted — deliberately excludes
     * Citizenship Copy / Passport Photo / Proof of Address (already
     * collected by KYC) and Authorization Letter / Power of Attorney
     * (its own dedicated feature) so nothing is asked for twice.
     *
     * @return array<int, Component>
     */
    public static function documentFields(): array
    {
        return [
            FileUpload::make('doc_ownership_certificate')
                ->label('Ownership Certificate Copy — Lalpurja (लालपुर्जा प्रतिलिपि)')
                ->disk('public')->directory('properties/documents')
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])
                ->maxSize(20480),
            FileUpload::make('doc_land_house')
                ->label('Land / House Documents (जग्गा/घर कागजात)')
                ->disk('public')->directory('properties/documents')
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])
                ->maxSize(20480),
            FileUpload::make('doc_tax_clearance')
                ->label('Tax Clearance Certificate (कर चुक्ता प्रमाणपत्र)')
                ->disk('public')->directory('properties/documents')
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])
                ->maxSize(20480),
            FileUpload::make('doc_utility_bills')
                ->label('Utility Bills (उपभोक्ता बिल)')
                ->disk('public')->directory('properties/documents')
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])
                ->maxSize(20480),
            FileUpload::make('doc_blueprint')
                ->label('Blueprint / Naksa (नक्सा)')
                ->disk('public')->directory('properties/documents')
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])
                ->maxSize(20480)
                ->visible(fn (Get $get) => in_array($get('property_type'), Property::BUILDING_TYPES, true)),
            FileUpload::make('doc_building_completion')
                ->label('Building Completion Certificate (घर नक्सा उत्तीर्ण प्रमाणपत्र)')
                ->disk('public')->directory('properties/documents')
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'])
                ->maxSize(20480)
                ->visible(fn (Get $get) => in_array($get('property_type'), Property::BUILDING_TYPES, true)),
        ];
    }

    /**
     * Maps the synthetic upload field keys above to their document_types
     * row, so submissions can be synced into the generic property_documents
     * checklist table the same way KYC syncs its own checklist.
     *
     * @return array<string, string>
     */
    public static function documentFieldMap(): array
    {
        return [
            'doc_ownership_certificate' => 'Ownership Certificate Copy',
            'doc_land_house' => 'Land/House Documents',
            'doc_tax_clearance' => 'Tax Clearance',
            'doc_utility_bills' => 'Utility Bills',
            'doc_blueprint' => 'Blueprint',
            'doc_building_completion' => 'Building Completion Certificate',
        ];
    }

    /**
     * Annex A §7 Property Features.
     *
     * @return array<int, Component>
     */
    public static function featureFields(): array
    {
        return [
            CheckboxList::make('feature_ids')
                ->label('')
                ->options(fn () => PropertyFeatureType::orderBy('feature_name')->pluck('feature_name', 'feature_id'))
                ->columns(2)
                ->gridDirection('row'),
        ];
    }

    /**
     * Annex A photographs of the property.
     *
     * @return array<int, Component>
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

    /**
     * Declaration & signature, matching the KYC wizard's closing step.
     *
     * @return array<int, Component>
     */
    public static function declarationFields(): array
    {
        return [
            Toggle::make('declaration_accepted')
                ->label('Declaration (घोषणा)')
                ->helperText('I hereby declare that the information provided above about this property is true and correct to the best of my knowledge, as required under Annex A of API GharJagga\'s property listing process.')
                ->required()
                ->accepted()
                ->inline(false),
            FileUpload::make('applicant_signature_path')
                ->label('Signature (हस्ताक्षर)')
                ->disk('public')
                ->directory('properties/signatures')
                ->image()
                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                ->maxSize(4096)
                ->required()
                ->openable()
                ->helperText('Upload a photo or scan of your handwritten signature (Max: 4MB).'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                Section::make('Applicant Details')
                    ->description('From your approved KYC record — update it there if anything below is wrong.')
                    ->icon('heroicon-o-identification')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema(static::applicantDetailsFields()),

                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Property Owner Details')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema(static::ownerDetailsFields()),

                        Section::make('Property Details')
                            ->icon('heroicon-o-home-modern')
                            ->schema(static::propertyDetailsFields()),

                        Section::make('Address of Property')
                            ->icon('heroicon-o-map')
                            ->columns(2)
                            ->schema(static::addressFields()),

                        Section::make('Land Information')
                            ->icon('heroicon-o-map-pin')
                            ->columns(2)
                            ->schema(static::landInformationFields()),

                        Section::make('Building Details (If Applicable)')
                            ->icon('heroicon-o-building-office-2')
                            ->columns(2)
                            ->visible(fn (Get $get) => in_array($get('property_type'), Property::BUILDING_TYPES, true))
                            ->schema(static::buildingDetailsFields()),

                        Section::make('Purpose of Listing')
                            ->icon('heroicon-o-tag')
                            ->schema(static::purposeFields()),

                        Section::make('Expected Price')
                            ->icon('heroicon-o-banknotes')
                            ->columns(2)
                            ->schema(static::priceFields()),
                    ]),

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
                            ]),
                    ]),

                Section::make('Property Documents Submitted')
                    ->icon('heroicon-o-document-check')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema(static::documentFields()),

                Section::make('Property Features')
                    ->icon('heroicon-o-sparkles')
                    ->columnSpanFull()
                    ->schema(static::featureFields()),

                Section::make('Property Photographs & Media')
                    ->icon('heroicon-o-camera')
                    ->columnSpanFull()
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
                    ->hidden(fn (Property $record) => ! static::canEdit($record)),
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
