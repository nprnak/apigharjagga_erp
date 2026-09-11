<?php

namespace App\Filament\User\Pages;

use App\Filament\Support\LocationSelects;
use App\Models\DocumentType;
use App\Models\KycOrganizationDetail;
use App\Models\KycPropertyRequirement;
use App\Models\KycServiceRequest;
use App\Models\KycVerification;
use App\Models\KycVerificationDocument;
use App\Models\ServiceType;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class KycVerificationPage extends Page
{
    protected string $view = 'filament.user.pages.kyc-verification-page';

    protected static ?string $navigationLabel = 'KYC Verification';

    protected static ?string $title = 'Client KYC Verification (Annex F)';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-check';
    }

    public ?array $data = [];

    public ?KycVerification $kycRecord = null;

    /**
     * Statuses that lock the form against further edits — only a fresh
     * applicant or one that was sent back by rejection may edit. Once
     * locked, the page shows a single read-only summary of everything
     * submitted instead of the wizard (see the blade view).
     */
    private const LOCKED_STATUSES = ['pending', 'verified', 'approved'];

    public function mount(): void
    {
        $user = Auth::user();
        $this->kycRecord = $user->kycVerification()
            ->with(['documents.docType', 'organization', 'propertyRequirement', 'serviceRequests.serviceType', 'verifiedBy', 'approvedBy'])
            ->first();

        if ($this->kycRecord) {
            $docs = $this->kycRecord->documents->keyBy(fn ($d) => $d->docType?->doc_name);

            $this->form->fill([
                'full_name' => $this->kycRecord->full_name ?? $user->name,
                'father_mother_name' => $this->kycRecord->father_mother_name,
                'grandfather_name' => $this->kycRecord->grandfather_name,
                'spouse_name' => $this->kycRecord->spouse_name,
                'citizenship_no' => $this->kycRecord->citizenship_no,
                'date_of_birth' => $this->kycRecord->date_of_birth?->format('Y-m-d'),
                'gender' => $this->kycRecord->gender,
                'nationality' => $this->kycRecord->nationality ?? 'Nepali',
                'occupation' => $this->kycRecord->occupation,
                'mobile_no' => $this->kycRecord->mobile_no,
                'alt_contact_no' => $this->kycRecord->alt_contact_no,
                'telephone_no' => $this->kycRecord->telephone_no,
                'email' => $this->kycRecord->email ?? $user->email,
                'permanent_province' => $this->kycRecord->permanent_province,
                'permanent_district' => $this->kycRecord->permanent_district,
                'permanent_municipality' => $this->kycRecord->permanent_municipality,
                'permanent_ward_no' => $this->kycRecord->permanent_ward_no,
                'permanent_tole' => $this->kycRecord->permanent_tole,
                'same_as_permanent' => $this->currentMatchesPermanent(),
                'current_province' => $this->kycRecord->current_province,
                'current_district' => $this->kycRecord->current_district,
                'current_municipality' => $this->kycRecord->current_municipality,
                'current_ward_no' => $this->kycRecord->current_ward_no,
                'current_tole' => $this->kycRecord->current_tole,
                'organization_name' => $this->kycRecord->organization?->organization_name,
                'registration_no' => $this->kycRecord->organization?->registration_no,
                'pan_vat_no' => $this->kycRecord->organization?->pan_vat_no,
                'authorized_person' => $this->kycRecord->organization?->authorized_person,
                'org_designation' => $this->kycRecord->organization?->designation,
                'office_address' => $this->kycRecord->organization?->office_address,
                'purpose' => $this->kycRecord->propertyRequirement?->purpose,
                'property_type' => $this->kycRecord->propertyRequirement?->property_type,
                'preferred_location' => $this->kycRecord->propertyRequirement?->preferred_location,
                'required_area' => $this->kycRecord->propertyRequirement?->required_area,
                'estimated_budget' => $this->kycRecord->propertyRequirement?->estimated_budget,
                'purchase_timeline' => $this->kycRecord->propertyRequirement?->purchase_timeline,
                'service_type_ids' => $this->kycRecord->serviceRequests->pluck('service_type_id')->all(),
                'id_type' => $this->kycRecord->id_type,
                'id_document_path' => $this->kycRecord->id_document_path,
                'selfie_photo_path' => $this->kycRecord->selfie_photo_path,
                'address_proof_path' => $docs->get('Proof of Current Address')?->file_ref,
                'signature_path' => $this->kycRecord->signature_path,
                'declaration_accepted' => (bool) $this->kycRecord->signature_path,
            ]);
        } else {
            $this->form->fill([
                'full_name' => $user->name,
                'email' => $user->email,
                'nationality' => 'Nepali',
                'same_as_permanent' => false,
                'service_type_ids' => [],
            ]);
        }
    }

    /**
     * Returns true when the saved current address is identical to the permanent
     * address, so the "same as permanent" toggle can be pre-checked on load.
     */
    protected function currentMatchesPermanent(): bool
    {
        $k = $this->kycRecord;

        if (! $k || empty($k->permanent_municipality)) {
            return false;
        }

        return $k->current_province === $k->permanent_province
            && $k->current_district === $k->permanent_district
            && $k->current_municipality === $k->permanent_municipality
            && $k->current_ward_no === $k->permanent_ward_no
            && $k->current_tole === $k->permanent_tole;
    }

    public function form(Schema $schema): Schema
    {
        $isLocked = in_array($this->kycRecord?->status, self::LOCKED_STATUSES, true);

        $submitLabel = $this->kycRecord?->status === 'rejected'
            ? 'Resubmit KYC Application'
            : 'Submit KYC for Verification';

        // Current-address dropdowns mirror the permanent ones. When the toggle
        // is on they are locked, but still dehydrated so the copied values save.
        $currentSelects = array_map(
            fn (Select $select): Select => $select
                ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent'))
                ->dehydrated(true),
            LocationSelects::make(
                province: 'current_province',
                district: 'current_district',
                municipality: 'current_municipality',
                ward: 'current_ward_no',
                required: false,
            ),
        );

        $wizard = Wizard::make([
            Step::make('Personal Details')
                ->label('Personal Details (व्यक्तिगत विवरण)')
                ->description('Identity as printed on your government ID')
                ->icon('heroicon-o-user-circle')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('full_name')
                            ->label('Full Legal Name (पूरा नाम)')
                            ->required()
                            ->maxLength(150)
                            ->columnSpanFull(),
                        TextInput::make('father_mother_name')
                            ->label("Father's / Mother's Name (बाबु/आमाको नाम)")
                            ->maxLength(150),
                        TextInput::make('grandfather_name')
                            ->label("Grandfather's Name (बाजेको नाम)")
                            ->maxLength(150),
                        TextInput::make('spouse_name')
                            ->label("Spouse's Name (पति/पत्नीको नाम)")
                            ->placeholder('If applicable')
                            ->maxLength(150),
                        TextInput::make('citizenship_no')
                            ->label('Citizenship / National ID Number (नागरिकता नं.)')
                            ->required()
                            ->maxLength(50),
                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth (जन्म मिति)')
                            ->required()
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d M Y'),
                        Select::make('gender')
                            ->label('Gender (लिङ्ग)')
                            ->options([
                                'male' => 'Male (पुरुष)',
                                'female' => 'Female (महिला)',
                                'other' => 'Other (अन्य)',
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('nationality')
                            ->label('Nationality (राष्ट्रियता)')
                            ->default('Nepali')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('occupation')
                            ->label('Occupation (पेशा)')
                            ->maxLength(100),
                        TextInput::make('mobile_no')
                            ->label('Mobile Number (मोबाइल नम्बर)')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        TextInput::make('alt_contact_no')
                            ->label('Alternate Contact No. (वैकल्पिक सम्पर्क नं.)')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('telephone_no')
                            ->label('Telephone No. (टेलिफोन नम्बर)')
                            ->tel()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Email Address (इमेल ठेगाना)')
                            ->email()
                            ->required()
                            ->maxLength(150),
                    ]),
                ]),

            Step::make('Residential Address')
                ->label('Residential Address (ठेगाना)')
                ->description('Permanent and current / temporary address')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    Section::make('Permanent Address (स्थायी ठेगाना)')
                        ->description('Must match the address on your citizenship certificate.')
                        ->columns(2)
                        ->schema([
                            ...LocationSelects::make(
                                province: 'permanent_province',
                                district: 'permanent_district',
                                municipality: 'permanent_municipality',
                                ward: 'permanent_ward_no',
                                required: true,
                            ),
                            TextInput::make('permanent_tole')
                                ->label('Tole / Locality / Landmark (टोल/स्थान)')
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Current / Temporary Address (हालको ठेगाना)')
                        ->description('Where you currently reside, if different from permanent.')
                        ->columns(2)
                        ->schema([
                            Toggle::make('same_as_permanent')
                                ->label('Same as permanent address (स्थायी ठेगाना जस्तै)')
                                ->inline(false)
                                ->live()
                                ->afterStateUpdated(function (bool $state, Get $get, Set $set): void {
                                    if (! $state) {
                                        return;
                                    }

                                    $set('current_province', $get('permanent_province'));
                                    $set('current_district', $get('permanent_district'));
                                    $set('current_municipality', $get('permanent_municipality'));
                                    $set('current_ward_no', $get('permanent_ward_no'));
                                    $set('current_tole', $get('permanent_tole'));
                                })
                                ->columnSpanFull(),
                            ...$currentSelects,
                            TextInput::make('current_tole')
                                ->label('Tole / Locality / Landmark (टोल/स्थान)')
                                ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent'))
                                ->dehydrated(true)
                                ->columnSpanFull(),
                        ]),
                ]),

            Step::make('Organization Details')
                ->label('Organization Details (संस्था विवरण)')
                ->description('Only if you are registering on behalf of a company or institution — leave blank otherwise')
                ->icon('heroicon-o-building-office')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('organization_name')
                            ->label('Organization Name (संस्थाको नाम)')
                            ->maxLength(200)
                            ->columnSpanFull(),
                        TextInput::make('registration_no')
                            ->label('Registration No. (दर्ता नं.)')
                            ->maxLength(50),
                        TextInput::make('pan_vat_no')
                            ->label('PAN / VAT No.')
                            ->maxLength(50),
                        TextInput::make('authorized_person')
                            ->label('Authorized Person (अख्तियार प्राप्त व्यक्ति)')
                            ->maxLength(150),
                        TextInput::make('org_designation')
                            ->label('Designation (पद)')
                            ->maxLength(100),
                        TextInput::make('office_address')
                            ->label('Office Address (कार्यालयको ठेगाना)')
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                ]),

            Step::make('Property Requirement & Services')
                ->label('Property Requirement & Services (आवश्यकता र सेवा)')
                ->description('For buyers, investors, and tenants — tell us what you\'re looking for')
                ->icon('heroicon-o-magnifying-glass')
                ->schema([
                    Section::make('Property Requirement Details (सम्पत्ति आवश्यकता)')
                        ->description('Optional — fill in if you are looking to buy, invest, or rent.')
                        ->columns(2)
                        ->schema([
                            Select::make('purpose')
                                ->label('Purpose (उद्देश्य)')
                                ->options([
                                    'purchase' => 'Purchase',
                                    'investment' => 'Investment',
                                    'rent' => 'Rent',
                                ])
                                ->native(false),
                            Select::make('property_type')
                                ->label('Property Type (सम्पत्तिको किसिम)')
                                ->options([
                                    'land' => 'Land',
                                    'house' => 'House',
                                    'apartment' => 'Apartment',
                                    'commercial' => 'Commercial',
                                    'other' => 'Other',
                                ])
                                ->native(false),
                            TextInput::make('preferred_location')
                                ->label('Preferred Location (रुचाइएको स्थान)')
                                ->maxLength(200),
                            TextInput::make('required_area')
                                ->label('Required Area (आवश्यक क्षेत्रफल)')
                                ->maxLength(100),
                            TextInput::make('estimated_budget')
                                ->label('Estimated Budget (अनुमानित बजेट)')
                                ->numeric()
                                ->prefix('Rs.'),
                            TextInput::make('purchase_timeline')
                                ->label('Purchase Timeline (समयसीमा)')
                                ->placeholder('e.g. Within 3 months')
                                ->maxLength(100),
                        ]),

                    Section::make('Required Service Selection (आवश्यक सेवा छनोट)')
                        ->description('Optional — select any services you would like us to provide.')
                        ->schema([
                            CheckboxList::make('service_type_ids')
                                ->label('')
                                ->options(fn () => ServiceType::where('is_active', true)->pluck('service_name', 'service_type_id'))
                                ->columns(2)
                                ->gridDirection('row'),
                        ]),
                ]),

            Step::make('Document Checklist')
                ->label('Document Checklist (कागजात सूची)')
                ->description('Upload each required document — Annex F cannot be approved until all three are provided')
                ->icon('heroicon-o-camera')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('id_type')
                            ->label('Government ID Type (परिचयपत्रको प्रकार)')
                            ->options([
                                'citizenship' => 'Citizenship Certificate',
                                'national_id' => 'National Identity Card (NID)',
                                'passport' => 'Passport',
                                'driving_license' => 'Driving License',
                            ])
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('id_document_path')
                            ->label('1. Citizenship / ID Copy (नागरिकताको प्रतिलिपि)')
                            ->disk('public')
                            ->directory('kyc/documents')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(20480)
                            ->required()
                            ->openable()
                            ->downloadable()
                            ->helperText('Scanned copy or crisp photograph of both sides of your official identity card (Max: 20MB).'),
                        FileUpload::make('selfie_photo_path')
                            ->label('2. Passport-Size Photo (पासपोर्ट साइज फोटो)')
                            ->disk('public')
                            ->directory('kyc/selfies')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '1:1',
                                '35:45',
                            ])
                            ->maxSize(20480)
                            ->required()
                            ->openable()
                            ->downloadable()
                            ->helperText('Front-facing passport-size (PP) photo against a plain or light background (Max: 20MB).'),
                        FileUpload::make('address_proof_path')
                            ->label('3. Proof of Current Address (हालको ठेगानाको प्रमाण)')
                            ->disk('public')
                            ->directory('kyc/address-proof')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'application/pdf'])
                            ->maxSize(20480)
                            ->required()
                            ->openable()
                            ->downloadable()
                            ->helperText('A recent utility bill, rental agreement, or ward recommendation showing your current address (Max: 20MB).')
                            ->columnSpanFull(),
                    ]),
                ]),

            Step::make('Declaration & Signature')
                ->label('Declaration & Signature (घोषणा र हस्ताक्षर)')
                ->description('Confirm the information provided is true and correct')
                ->icon('heroicon-o-pencil-square')
                ->schema([
                    Section::make()
                        ->schema([
                            Toggle::make('declaration_accepted')
                                ->label('Declaration (घोषणा)')
                                ->helperText('I hereby declare that the information and documents provided above are true and correct to the best of my knowledge, as required under Annex F of API GharJagga\'s client registration process.')
                                ->required()
                                ->accepted()
                                ->inline(false),
                            FileUpload::make('signature_path')
                                ->label('Signature (हस्ताक्षर)')
                                ->disk('public')
                                ->directory('kyc/signatures')
                                ->image()
                                ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                                ->maxSize(4096)
                                ->required()
                                ->openable()
                                ->helperText('Upload a photo or scan of your handwritten signature (Max: 4MB).'),
                        ]),
                ]),
        ])
            ->persistStepInQueryString('kyc-step');

        // Only expose the final submit button while the form is editable.
        if (! $isLocked) {
            $wizard->submitAction(new HtmlString(Blade::render(
                '<x-filament::button type="submit" size="lg" icon="heroicon-m-check">{{ $label }}</x-filament::button>',
                ['label' => $submitLabel],
            )));
        }

        return $schema
            ->statePath('data')
            ->disabled($isLocked)
            ->components([$wizard]);
    }

    public function submit(): void
    {
        $user = Auth::user();

        if (in_array($this->kycRecord?->status, self::LOCKED_STATUSES, true)) {
            Notification::make()
                ->title('Submission Locked')
                ->body('Your KYC is currently '.match ($this->kycRecord?->status) {
                    'approved' => 'approved.',
                    'verified' => 'verified and awaiting final approval.',
                    default => 'under review.',
                })
                ->warning()
                ->send();

            return;
        }

        $state = $this->form->getState();

        // UI-only helper flag and Annex F §4/§5/§7 fields — not columns on
        // kyc_verifications itself, pulled out here and saved to their own
        // tables below.
        $addressProofPath = $state['address_proof_path'] ?? null;
        $organizationData = [
            'organization_name' => $state['organization_name'] ?? null,
            'registration_no' => $state['registration_no'] ?? null,
            'pan_vat_no' => $state['pan_vat_no'] ?? null,
            'authorized_person' => $state['authorized_person'] ?? null,
            'designation' => $state['org_designation'] ?? null,
            'office_address' => $state['office_address'] ?? null,
        ];
        $propertyRequirementData = [
            'purpose' => $state['purpose'] ?? null,
            'property_type' => $state['property_type'] ?? null,
            'preferred_location' => $state['preferred_location'] ?? null,
            'required_area' => $state['required_area'] ?? null,
            'estimated_budget' => $state['estimated_budget'] ?? null,
            'purchase_timeline' => $state['purchase_timeline'] ?? null,
        ];
        $serviceTypeIds = $state['service_type_ids'] ?? [];

        unset(
            $state['same_as_permanent'], $state['address_proof_path'], $state['declaration_accepted'],
            $state['organization_name'], $state['registration_no'], $state['pan_vat_no'],
            $state['authorized_person'], $state['org_designation'], $state['office_address'],
            $state['purpose'], $state['property_type'], $state['preferred_location'],
            $state['required_area'], $state['estimated_budget'], $state['purchase_timeline'],
            $state['service_type_ids'],
        );

        $payload = array_merge($state, [
            'status' => 'pending',
            'admin_note' => null,
            'submitted_at' => now(),
            'reviewed_at' => null,
            'verified_by_staff_id' => null,
            'verified_at' => null,
            'approved_by_staff_id' => null,
            'approved_at' => null,
            'signature_date' => now(),
        ]);

        if ($this->kycRecord) {
            $this->kycRecord->update($payload);
        } else {
            $this->kycRecord = $user->kycVerification()->create($payload);
        }

        $this->syncDocumentChecklist($addressProofPath);
        $this->syncOrganization($organizationData);
        $this->syncPropertyRequirement($propertyRequirementData);
        $this->syncServiceRequests($serviceTypeIds);

        $this->kycRecord->load(['documents.docType', 'organization', 'propertyRequirement', 'serviceRequests.serviceType']);

        Notification::make()
            ->title('KYC Verification Submitted Successfully')
            ->body('Our verification officers will review your documents shortly.')
            ->success()
            ->send();
    }

    /**
     * Keeps the Annex-F document checklist (kyc_verification_documents) in
     * sync with the three uploads collected above, so the admin resource
     * and PDF export can list checklist status generically rather than
     * reading three differently-named columns.
     */
    private function syncDocumentChecklist(?string $addressProofPath): void
    {
        $checklist = [
            'Citizenship Copy' => $this->kycRecord->id_document_path,
            'Passport Size Photo' => $this->kycRecord->selfie_photo_path,
            'Proof of Current Address' => $addressProofPath,
        ];

        foreach ($checklist as $docName => $fileRef) {
            $docType = DocumentType::firstOrCreate(['doc_name' => $docName], ['category' => 'identity']);

            KycVerificationDocument::updateOrCreate(
                ['kyc_verification_id' => $this->kycRecord->id, 'doc_type_id' => $docType->doc_type_id],
                ['file_ref' => $fileRef, 'status' => $fileRef ? 'submitted' : 'pending', 'updated_at' => now()],
            );
        }
    }

    /**
     * Annex F §4 is explicitly "if applicable" — only keep a row while at
     * least one field is filled in, so an individual applicant's record
     * doesn't carry a meaningless all-null organization row.
     */
    private function syncOrganization(array $data): void
    {
        if (collect($data)->filter(fn ($v) => filled($v))->isEmpty()) {
            $this->kycRecord->organization()->delete();

            return;
        }

        KycOrganizationDetail::updateOrCreate(['kyc_verification_id' => $this->kycRecord->id], $data);
    }

    /**
     * Annex F §5 is optional (mainly relevant to Buyer/Investor/Tenant) —
     * same "only keep if filled in" rule as the organization section.
     */
    private function syncPropertyRequirement(array $data): void
    {
        if (collect($data)->filter(fn ($v) => filled($v))->isEmpty()) {
            $this->kycRecord->propertyRequirement()->delete();

            return;
        }

        KycPropertyRequirement::updateOrCreate(
            ['kyc_verification_id' => $this->kycRecord->id],
            array_merge($data, ['updated_at' => now()]),
        );
    }

    /**
     * Annex F §7 — a simple sync against the selected service_type_ids.
     */
    private function syncServiceRequests(array $serviceTypeIds): void
    {
        $this->kycRecord->serviceRequests()->whereNotIn('service_type_id', $serviceTypeIds)->delete();

        foreach ($serviceTypeIds as $serviceTypeId) {
            KycServiceRequest::firstOrCreate([
                'kyc_verification_id' => $this->kycRecord->id,
                'service_type_id' => $serviceTypeId,
            ]);
        }
    }
}
