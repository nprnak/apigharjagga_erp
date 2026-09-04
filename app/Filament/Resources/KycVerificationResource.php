<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\KycVerificationResource\Pages;
use App\Filament\Support\LocationSelects;
use App\Models\KycVerification;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class KycVerificationResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = KycVerification::class;

    protected static function permissionKey(): string
    {
        return 'kyc';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-check';
    }

    protected static ?string $navigationLabel = 'KYC Verifications';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|null
    {
        return 'Users & KYC';
    }

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        // Two-column workspace on large screens: applicant details on the left,
        // the ID documents and submission meta the reviewer needs alongside them
        // on the right. Collapses to a single stacked column below `lg`.
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                // The decision band sits full-width above the fold — it is why
                // an admin opens the record.
                Section::make('Review Decision')
                    ->description('Set the verification outcome. The note is shown to the user when rejected.')
                    ->icon('heroicon-o-shield-check')
                    ->columns(3)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                            ->required()
                            ->native(false),
                        Textarea::make('admin_note')
                            ->label('Admin Note (shown to user if rejected)')
                            ->rows(2)
                            ->columnSpan(2),
                    ]),

                // ---- Applicant details, main column -----------------------
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 2])
                    ->schema([
                        Section::make('Personal Information')
                            ->icon('heroicon-o-user')
                            ->columns(2)
                            ->schema([
                                TextInput::make('full_name')->label('Full Name')->maxLength(150),
                                TextInput::make('father_mother_name')->label('Father / Mother Name')->maxLength(150),
                                TextInput::make('spouse_name')->label('Spouse Name')->maxLength(150),
                                TextInput::make('citizenship_no')->label('Citizenship No.')->maxLength(50),
                                DatePicker::make('date_of_birth')->label('Date of Birth'),
                                Select::make('gender')->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'])->native(false),
                                TextInput::make('nationality')->default('Nepali')->maxLength(50),
                                TextInput::make('occupation')->maxLength(100),
                                TextInput::make('mobile_no')->label('Mobile No.')->tel()->maxLength(20),
                                TextInput::make('email')->email()->maxLength(150),
                            ]),

                        Section::make('Permanent Address')
                            ->icon('heroicon-o-map-pin')
                            ->columns(2)
                            ->collapsible()
                            ->schema([
                                ...LocationSelects::make(
                                    province: 'permanent_province',
                                    district: 'permanent_district',
                                    municipality: 'permanent_municipality',
                                    ward: 'permanent_ward_no',
                                    required: false,
                                    labels: [
                                        'province' => 'Province',
                                        'district' => 'District',
                                        'municipality' => 'Municipality / VDC',
                                        'ward' => 'Ward No.',
                                    ],
                                ),
                                TextInput::make('permanent_tole')->label('Tole / Locality')->columnSpanFull(),
                            ]),

                        Section::make('Current Address')
                            ->icon('heroicon-o-map')
                            ->columns(2)
                            ->collapsible()
                            ->schema([
                                ...LocationSelects::make(
                                    province: 'current_province',
                                    district: 'current_district',
                                    municipality: 'current_municipality',
                                    ward: 'current_ward_no',
                                    required: false,
                                    labels: [
                                        'province' => 'Province',
                                        'district' => 'District',
                                        'municipality' => 'Municipality / VDC',
                                        'ward' => 'Ward No.',
                                    ],
                                ),
                                TextInput::make('current_tole')->label('Tole / Locality')->columnSpanFull(),
                            ]),
                    ]),

                // ---- Documents + submission meta, sidebar -----------------
                Group::make()
                    ->columnSpan(['default' => 1, 'lg' => 1])
                    ->schema([
                        Section::make('Identity Documents')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Select::make('id_type')
                                    ->label('ID Type')
                                    ->options([
                                        'citizenship'     => 'Citizenship Card',
                                        'national_id'     => 'National ID',
                                        'passport'        => 'Passport',
                                        'driving_license' => 'Driving License',
                                    ])
                                    ->native(false),
                                FileUpload::make('selfie_photo_path')
                                    ->label('Passport-Size Photo (PP Photo)')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                                    ->maxSize(20480)
                                    ->disk('public')
                                    ->openable()
                                    ->downloadable(),
                                FileUpload::make('id_document_path')
                                    ->label('Identity Document (Citizenship/NID/Passport)')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
                                    ->maxSize(20480)
                                    ->disk('public')
                                    ->openable()
                                    ->downloadable(),
                            ]),

                        Section::make('Submission')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Placeholder::make('user_account')
                                    ->label('User Account')
                                    ->content(fn (?KycVerification $record) => $record?->user
                                        ? collect([$record->user->name, $record->user->email])->filter()->implode(' · ') ?: '—'
                                        : '—'),
                                DateTimePicker::make('submitted_at')->label('Submitted At')->disabled(),
                                DateTimePicker::make('reviewed_at')->label('Reviewed At')->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('selfie_photo_path')
                    ->label('PP Photo')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_type')
                    ->label('ID Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'citizenship'     => 'Citizenship',
                        'national_id'     => 'National ID',
                        'passport'        => 'Passport',
                        'driving_license' => 'Driving License',
                        default           => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewed_at')
                    ->label('Reviewed')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\SelectFilter::make('id_type')
                    ->label('ID Type')
                    ->options([
                        'citizenship'     => 'Citizenship',
                        'national_id'     => 'National ID',
                        'passport'        => 'Passport',
                        'driving_license' => 'Driving License',
                    ]),
            ])
            ->actions([
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (KycVerification $record) => $record->status === 'approved')
                    ->action(function (KycVerification $record) {
                        $record->update(['status' => 'approved', 'reviewed_at' => now(), 'admin_note' => null]);
                        Notification::make()->title('KYC approved')->success()->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->hidden(fn (KycVerification $record) => $record->status === 'rejected')
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Rejection reason (shown to user)')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (KycVerification $record, array $data) {
                        $record->update([
                            'status'     => 'rejected',
                            'admin_note' => $data['admin_note'],
                            'reviewed_at' => now(),
                        ]);
                        Notification::make()->title('KYC rejected')->danger()->send();
                    }),
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKycVerifications::route('/'),
            'create' => Pages\CreateKycVerification::route('/create'),
            'view'   => Pages\ViewKycVerification::route('/{record}'),
            'edit'   => Pages\EditKycVerification::route('/{record}/edit'),
        ];
    }
}
