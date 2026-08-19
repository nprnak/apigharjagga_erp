<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KycVerificationResource\Pages;
use App\Models\KycVerification;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class KycVerificationResource extends Resource
{
    protected static ?string $model = KycVerification::class;

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
        return $schema->components([
            Section::make('Personal Information')
                ->columns(2)
                ->schema([
                    TextInput::make('full_name')->label('Full Name')->maxLength(150),
                    TextInput::make('father_mother_name')->label("Father / Mother Name")->maxLength(150),
                    TextInput::make('spouse_name')->label('Spouse Name')->maxLength(150),
                    TextInput::make('citizenship_no')->label('Citizenship No.')->maxLength(50),
                    DatePicker::make('date_of_birth')->label('Date of Birth'),
                    Select::make('gender')->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other']),
                    TextInput::make('nationality')->default('Nepali')->maxLength(50),
                    TextInput::make('occupation')->maxLength(100),
                    TextInput::make('mobile_no')->label('Mobile No.')->tel()->maxLength(20),
                    TextInput::make('email')->email()->maxLength(150),
                ]),

            Section::make('Permanent Address')
                ->columns(2)
                ->schema([
                    TextInput::make('permanent_province')->label('Province'),
                    TextInput::make('permanent_district')->label('District'),
                    TextInput::make('permanent_municipality')->label('Municipality / VDC'),
                    TextInput::make('permanent_ward_no')->label('Ward No.'),
                    TextInput::make('permanent_tole')->label('Tole / Locality')->columnSpanFull(),
                ]),

            Section::make('Current Address')
                ->columns(2)
                ->schema([
                    TextInput::make('current_province')->label('Province'),
                    TextInput::make('current_district')->label('District'),
                    TextInput::make('current_municipality')->label('Municipality / VDC'),
                    TextInput::make('current_ward_no')->label('Ward No.'),
                    TextInput::make('current_tole')->label('Tole / Locality')->columnSpanFull(),
                ]),

            Section::make('Document & Status')
                ->columns(2)
                ->schema([
                    Select::make('id_type')
                        ->label('ID Type')
                        ->options([
                            'citizenship'     => 'Citizenship Card',
                            'national_id'     => 'National ID',
                            'passport'        => 'Passport',
                            'driving_license' => 'Driving License',
                        ]),
                    Select::make('status')
                        ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                        ->required(),
                    Textarea::make('admin_note')
                        ->label('Admin Note (shown to user if rejected)')
                        ->rows(3)
                        ->columnSpanFull(),
                    DateTimePicker::make('submitted_at')->label('Submitted At')->disabled(),
                    DateTimePicker::make('reviewed_at')->label('Reviewed At')->disabled(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
