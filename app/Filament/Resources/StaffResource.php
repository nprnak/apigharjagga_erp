<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers\ContractsRelationManager;
use App\Filament\Resources\StaffResource\RelationManagers\DocumentsRelationManager;
use App\Models\Role;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

    public static function getNavigationGroup(): ?string
    {
        return 'Users & KYC';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('role');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Staff Details')
                ->columns(2)
                ->schema([
                    TextInput::make('employee_code')
                        ->maxLength(30)
                        ->unique(ignoreRecord: true),
                    TextInput::make('full_name')
                        ->required()
                        ->maxLength(150),
                    Select::make('role_id')
                        ->label('Role')
                        ->options(fn () => Role::pluck('role_name', 'role_id'))
                        ->required(),
                    TextInput::make('designation')
                        ->maxLength(100),
                    TextInput::make('department')
                        ->maxLength(100),
                    Select::make('employment_type')
                        ->options([
                            'full_time' => 'Full Time', 'part_time' => 'Part Time',
                            'contract' => 'Contract', 'probation' => 'Probation',
                        ])
                        ->default('full_time')
                        ->native(false),
                    DatePicker::make('date_of_joining'),
                    TextInput::make('basic_salary')
                        ->numeric()
                        ->prefix('Rs.'),
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
            Section::make('Personal & Emergency Contact')
                ->columns(2)
                ->collapsible()
                ->schema([
                    DatePicker::make('date_of_birth'),
                    Select::make('gender')
                        ->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'])
                        ->native(false),
                    TextInput::make('address')
                        ->maxLength(255)
                        ->columnSpanFull(),
                    TextInput::make('emergency_contact_name')
                        ->maxLength(150),
                    TextInput::make('emergency_contact_phone')
                        ->tel()
                        ->maxLength(20),
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

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            ContractsRelationManager::class,
        ];
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
