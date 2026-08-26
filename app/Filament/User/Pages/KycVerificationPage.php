<?php

namespace App\Filament\User\Pages;

use App\Models\KycVerification;
use App\Filament\Support\LocationSelects;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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

    public function mount(): void
    {
        $user = Auth::user();
        $this->kycRecord = $user->kycVerification;

        if ($this->kycRecord) {
            $this->form->fill([
                'full_name' => $this->kycRecord->full_name ?? $user->name,
                'father_mother_name' => $this->kycRecord->father_mother_name,
                'spouse_name' => $this->kycRecord->spouse_name,
                'citizenship_no' => $this->kycRecord->citizenship_no,
                'date_of_birth' => $this->kycRecord->date_of_birth?->format('Y-m-d'),
                'gender' => $this->kycRecord->gender,
                'nationality' => $this->kycRecord->nationality ?? 'Nepali',
                'occupation' => $this->kycRecord->occupation,
                'mobile_no' => $this->kycRecord->mobile_no,
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
        $isApproved = $this->kycRecord?->status === 'approved';
        $isPending = $this->kycRecord?->status === 'pending';
        $isLocked = $isApproved || $isPending;

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
                labels: [
                    'province' => 'Province',
                    'district' => 'District',
                    'municipality' => 'Municipality / Rural Municipality',
                    'ward' => 'Ward Number',
                ],
            ),
        );

        $wizard = Wizard::make([
            Step::make('Personal Details')
                ->description('Identity as printed on your government ID')
                ->icon('heroicon-o-user-circle')
                ->completedIcon('heroicon-m-check-badge')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('full_name')
                            ->label('Full Legal Name')
                            ->required()
                            ->maxLength(150)
                            ->prefixIcon('heroicon-m-user')
                            ->columnSpanFull(),
                        TextInput::make('father_mother_name')
                            ->label("Father / Mother's Full Name")
                            ->maxLength(150),
                        TextInput::make('spouse_name')
                            ->label("Spouse's Name")
                            ->placeholder('If applicable')
                            ->maxLength(150),
                        TextInput::make('citizenship_no')
                            ->label('Citizenship / National ID Number')
                            ->required()
                            ->maxLength(50)
                            ->prefixIcon('heroicon-m-identification'),
                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->required()
                            ->maxDate(now())
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-m-calendar-days'),
                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('nationality')
                            ->label('Nationality')
                            ->default('Nepali')
                            ->required()
                            ->maxLength(50)
                            ->prefixIcon('heroicon-m-flag'),
                        TextInput::make('occupation')
                            ->label('Profession / Occupation')
                            ->maxLength(100)
                            ->prefixIcon('heroicon-m-briefcase'),
                        TextInput::make('mobile_no')
                            ->label('Primary Mobile Number')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-phone'),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(150)
                            ->prefixIcon('heroicon-m-envelope'),
                    ]),
                ]),

            Step::make('Residential Address')
                ->description('Permanent and current / temporary address')
                ->icon('heroicon-o-map-pin')
                ->completedIcon('heroicon-m-check-badge')
                ->schema([
                    Section::make('Permanent Address')
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
                                labels: [
                                    'province' => 'Province',
                                    'district' => 'District',
                                    'municipality' => 'Municipality / Rural Municipality',
                                    'ward' => 'Ward Number',
                                ],
                            ),
                            TextInput::make('permanent_tole')
                                ->label('Tole / Locality / Landmark')
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    Section::make('Current / Temporary Address')
                        ->description('Where you currently reside, if different from permanent.')
                        ->icon('heroicon-m-map')
                        ->columns(2)
                        ->schema([
                            Toggle::make('same_as_permanent')
                                ->label('Same as permanent address')
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
                                ->label('Tole / Locality / Landmark')
                                ->disabled(fn (Get $get): bool => (bool) $get('same_as_permanent'))
                                ->dehydrated(true)
                                ->columnSpanFull(),
                        ]),
                ]),

            Step::make('Photograph & Documents')
                ->description('Passport photo and identity document')
                ->icon('heroicon-o-camera')
                ->completedIcon('heroicon-m-check-badge')
                ->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('selfie_photo_path')
                            ->label('Passport-Size Photo of Applicant (PP Size Photograph)')
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
                            ->helperText('Front-facing passport-size (PP) photo against a plain or light background (Max: 20MB).')
                            ->columnSpanFull(),
                        Select::make('id_type')
                            ->label('Government Document Type')
                            ->options([
                                'citizenship' => 'Citizenship Certificate',
                                'national_id' => 'National Identity Card (NID)',
                                'passport' => 'Passport',
                                'driving_license' => 'Driving License',
                            ])
                            ->native(false)
                            ->prefixIcon('heroicon-m-document-check')
                            ->required(),
                        FileUpload::make('id_document_path')
                            ->label('Identity Document Photo / Scanned Copy')
                            ->disk('public')
                            ->directory('kyc/documents')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                            ->maxSize(20480)
                            ->required()
                            ->openable()
                            ->downloadable()
                            ->helperText('Scanned copy or crisp photograph of both sides of your official identity card (Max: 20MB).'),
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

        if ($this->kycRecord?->status === 'approved' || $this->kycRecord?->status === 'pending') {
            Notification::make()
                ->title('Submission Locked')
                ->body('Your KYC is currently '.($this->kycRecord?->status === 'approved' ? 'already verified.' : 'under review.'))
                ->warning()
                ->send();

            return;
        }

        $state = $this->form->getState();

        // "same_as_permanent" is a UI-only helper flag, not a database column.
        unset($state['same_as_permanent']);

        $payload = array_merge($state, [
            'status' => 'pending',
            'admin_note' => null,
            'submitted_at' => now(),
            'reviewed_at' => null,
        ]);

        if ($this->kycRecord) {
            $this->kycRecord->update($payload);
        } else {
            $this->kycRecord = $user->kycVerification()->create($payload);
        }

        Notification::make()
            ->title('KYC Verification Submitted Successfully')
            ->body('Our verification officers will review your documents within 24 hours.')
            ->success()
            ->send();
    }
}
