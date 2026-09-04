<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?string $recordTitleAttribute = 'role_name';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-check';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Access Control';
    }

    // Only the super admin (Admin role, or an admin with no staff role assigned) may manage roles/permissions.
    public static function canViewAny(): bool
    {
        return static::currentUserIsSuperAdmin();
    }

    public static function canCreate(): bool
    {
        return static::currentUserIsSuperAdmin();
    }

    public static function canEdit($record): bool
    {
        return static::currentUserIsSuperAdmin();
    }

    public static function canDelete($record): bool
    {
        // The Admin role must always exist so a super admin can never lock everyone out.
        return static::currentUserIsSuperAdmin() && $record->role_name !== 'Admin';
    }

    protected static function currentUserIsSuperAdmin(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->isSuperAdmin();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withCount('users');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role Details')
                ->columns(1)
                ->schema([
                    TextInput::make('role_name')
                        ->label('Role Name')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    CheckboxList::make('permissions')
                        ->label('Permissions')
                        ->options(Role::availablePermissions())
                        ->helperText("'Full Access' grants every permission and makes the individual selections below redundant.")
                        ->columns(2)
                        ->required(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role_name')
                    ->label('Role')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions')
                    ->label('Permissions')
                    // Filament treats an array attribute as a multi-value column and
                    // calls formatStateUsing() per item, so pre-join to a plain string here.
                    ->getStateUsing(fn (Role $record) => filled($record->permissions)
                        ? collect($record->permissions)->map(fn (string $p) => Role::availablePermissions()[$p] ?? $p)->join(', ')
                        : '—')
                    ->wrap(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users Assigned')
                    ->sortable(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('role_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
