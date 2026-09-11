<?php

namespace App\Filament\User\Pages;

use App\Filament\Support\LocationSelects;
use App\Models\DocumentType;
use App\Models\KycVerification;
use App\Models\KycVerificationDocument;
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
     * applicant or one that was sent back by rejection may edit.
     */
    private const LOCKED_STATUSES = ['pending', 'verified', 'approved'];

    public function mount(): void
    {
        $user = Auth::user();
        $this->kycRecord = $user->kycVerification()->with('documents')->first();

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
                ->completedIcon('heroicon-m-check-badge')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('full_name')
                            ->label('Full Legal Name (पूरा नाम)')
                            ->required()
                            ->maxLength(150)
                            ->prefixIcon('heroicon-m-user')
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
                            ->maxLength(50)
                            ->prefixIcon('heroicon-m-identification'),
                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth (जन्म मिति)')
                            ->required()
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-m-calendar-days'),
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
                            ->maxLength(50)
                            ->prefixIcon('heroicon-m-flag'),
                        TextInput::make('occupation')
                            ->label('Occupation (पेशा)')
                            ->maxLength(100)
                            ->prefixIcon('heroicon-m-briefcase'),
                        TextInput::make('mobile_no')
                            ->label('Mobile Number (मोबाइल नम्बर)')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-phone'),
                        TextInput::make('alt_contact_no')
                            ->label('Alternate Contact No. (वैकल्पिक सम्पर्क नं.)')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-device-phone-mobile'),
                        TextInput::make('telephone_no')
                            ->label('Telephone No. (टेलिफोन नम्बर)')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-phone-arrow-up-right'),
                        TextInput::make('email')
                            ->label('Email Address (इमेल ठेगाना)')
                            ->email()
                            ->required()
                            ->maxLength(150)
                            ->prefixIcon('heroicon-m-envelope'),
                    ]),
                ]),

            Step::make('Residential Address')
                ->label('Residential Address (ठेगाना)')
                ->description('Permanent and current / temporary address')
                ->icon('heroicon-o-map-pin')
                ->completedIcon('heroicon-m-check-badge')
                ->schema([
                    Section::make('Permanent Address (स्थायी ठेगाना)')
                        ->description('Must match the address on your citizenship certificate.')
                        ->icon('heroicon-m-home-modern')
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
                        ->icon('heroicon-m-map')
                        ->columns(2)
                        ->schema([
                            Toggle::make('same_as_permanent')
                                ->label('Same as permanent address (स्थायी ठेगाना जस्तै)')
                                ->helperText('Copies every permanent address field into the current address below.')
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

            Step::make('Document Checklist')
                ->label('Document Checklist (कागजात सूची)')
                ->description('Upload each required document — Annex F cannot be approved until all three are provided')
                ->icon('heroicon-o-camera')
                ->completedIcon('heroicon-m-check-badge')
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
                            ->prefixIcon('heroicon-m-document-check')
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
                ->completedIcon('heroicon-m-check-badge')
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

        // UI-only helper flags — not database columns.
        $addressProofPath = $state['address_proof_path'] ?? null;
        unset($state['same_as_permanent'], $state['address_proof_path'], $state['declaration_accepted']);

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
}
