<?php

namespace App\Filament\User\Pages;

use App\Models\KycVerification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

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
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        $isApproved = $this->kycRecord?->status === 'approved';
        $isPending = $this->kycRecord?->status === 'pending';
        $isLocked = $isApproved || $isPending;

        return $schema
            ->statePath('data')
            ->disabled($isLocked)
            ->components([
                Section::make('1. Personal & Family Information')
                    ->description('Personal details as registered in official Government IDs')
                    ->columns(2)
                    ->schema([
                        TextInput::make('full_name')
                            ->label('Full Legal Name')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('father_mother_name')
                            ->label("Father / Mother's Full Name")
                            ->maxLength(150),
                        TextInput::make('spouse_name')
                            ->label("Spouse's Name (if applicable)")
                            ->maxLength(150),
                        TextInput::make('citizenship_no')
                            ->label('Citizenship / National ID Number')
                            ->required()
                            ->maxLength(50),
                        DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->required()
                            ->maxDate(now()),
                        Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'other' => 'Other',
                            ])
                            ->required(),
                        TextInput::make('nationality')
                            ->label('Nationality')
                            ->default('Nepali')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('occupation')
                            ->label('Profession / Occupation')
                            ->maxLength(100),
                        TextInput::make('mobile_no')
                            ->label('Primary Mobile Number')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(150),
                    ]),

                Section::make('2. Permanent Residence Address')
                    ->description('Address as indicated on citizenship certificate')
                    ->columns(2)
                    ->schema([
                        TextInput::make('permanent_province')->label('Province')->required(),
                        TextInput::make('permanent_district')->label('District')->required(),
                        TextInput::make('permanent_municipality')->label('Municipality / Rural Municipality')->required(),
                        TextInput::make('permanent_ward_no')->label('Ward Number')->required(),
                        TextInput::make('permanent_tole')->label('Tole / Locality / Landmark')->columnSpanFull()->required(),
                    ]),

                Section::make('3. Current / Temporary Address')
                    ->description('Present residential address for correspondence')
                    ->columns(2)
                    ->schema([
                        TextInput::make('current_province')->label('Province'),
                        TextInput::make('current_district')->label('District'),
                        TextInput::make('current_municipality')->label('Municipality / Rural Municipality'),
                        TextInput::make('current_ward_no')->label('Ward Number'),
                        TextInput::make('current_tole')->label('Tole / Locality / Landmark')->columnSpanFull(),
                    ]),

                Section::make('4. Identity Verification Documents')
                    ->description('Clear scanned copies or photographs of your government-issued ID')
                    ->columns(2)
                    ->schema([
                        Select::make('id_type')
                            ->label('Government Document Type')
                            ->options([
                                'citizenship' => 'Citizenship Certificate',
                                'national_id' => 'National Identity Card (NID)',
                                'passport' => 'Passport',
                                'driving_license' => 'Driving License',
                            ])
                            ->required(),
                        FileUpload::make('id_document_path')
                            ->label('Identity Document Photo / Scan')
                            ->disk('public')
                            ->directory('kyc/documents')
                            ->image()
                            ->maxSize(4096)
                            ->required(),
                        FileUpload::make('selfie_photo_path')
                            ->label('Recent Photograph / Clear Face Photo')
                            ->disk('public')
                            ->directory('kyc/selfies')
                            ->image()
                            ->maxSize(4096)
                            ->columnSpanFull()
                            ->helperText('Please upload a clear portrait photo showing your face in good lighting.'),
                    ]),
            ]);
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
