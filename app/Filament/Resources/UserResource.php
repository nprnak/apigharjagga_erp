<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Role;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function permissionKey(): string
    {
        return 'users';
    }

    protected static function currentUserIsSuperAdmin(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->isSuperAdmin();
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-users';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Users & KYC';
    }

    public static function getEloquentQuery(): Builder
    {
        // The table columns below render staffRole/kycVerification, so eager
        // load them here to avoid an N+1 query per row.
        return parent::getEloquentQuery()->with(['staffRole', 'kycVerification']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account Details')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Select::make('role')
                        ->options(['user' => 'User', 'admin' => 'Admin'])
                        ->live()
                        ->required(),
                    Select::make('role_id')
                        ->label('Staff Role')
                        ->options(fn () => Role::pluck('role_name', 'role_id'))
                        ->helperText('Controls which admin resources this user can access. Leave blank for full access.')
                        ->visible(fn ($get) => $get('role') === 'admin' && static::currentUserIsSuperAdmin())
                        ->nullable(),
                    DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At'),
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->label('New Password (leave blank to keep current)'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'admin' ? 'danger' : 'primary'),
                Tables\Columns\TextColumn::make('staffRole.role_name')
                    ->label('Staff Role')
                    ->placeholder('Full access')
                    ->badge(),
                Tables\Columns\TextColumn::make('kycVerification.status')
                    ->label('KYC')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state) => $state ?? 'None'),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(['user' => 'User', 'admin' => 'Admin']),
                Tables\Filters\Filter::make('kyc_approved')
                    ->label('KYC Approved')
                    ->query(fn (Builder $q) => $q->whereHas('kycVerification', fn ($q) => $q->where('status', 'approved'))),
                Tables\Filters\Filter::make('kyc_pending')
                    ->label('KYC Pending')
                    ->query(fn (Builder $q) => $q->whereHas('kycVerification', fn ($q) => $q->where('status', 'pending'))),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
