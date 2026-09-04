<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\StaffResource\Pages;
use App\Models\Role;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StaffResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Staff::class;

    protected static ?string $navigationLabel = 'Staff Directory';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'full_name';

    protected static function permissionKey(): string
    {
        return 'staff';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-identification';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Users & KYC';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('role');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Staff Details')
                ->columns(2)
                ->schema([
                    TextInput::make('full_name')
                        ->required()
                        ->maxLength(150),
                    Select::make('role_id')
                        ->label('Role')
                        ->options(fn () => Role::pluck('role_name', 'role_id'))
                        ->required(),
                    TextInput::make('designation')
                        ->maxLength(100),
                    TextInput::make('mobile_no')
                        ->tel()
                        ->maxLength(20),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(150),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role.role_name')
                    ->label('Role')
                    ->badge(),
                Tables\Columns\TextColumn::make('designation')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('mobile_no')
                    ->label('Mobile')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('email')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Role')
                    ->options(fn () => Role::pluck('role_name', 'role_id')),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('full_name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
