<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ContractorResource\Pages;
use App\Models\Contractor;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContractorResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Contractor::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function permissionKey(): string
    {
        return 'contractors';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-wrench-screwdriver';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Engineering & Projects';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Contractor')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(150),
                    TextInput::make('specialization')
                        ->maxLength(150),
                    TextInput::make('contact_person')
                        ->maxLength(150),
                    TextInput::make('mobile_no')
                        ->tel()
                        ->maxLength(20),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(150),
                    Toggle::make('is_active')
                        ->default(true),
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
                Tables\Columns\TextColumn::make('specialization')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('mobile_no')
                    ->label('Mobile')
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContractors::route('/'),
            'create' => Pages\CreateContractor::route('/create'),
            'edit' => Pages\EditContractor::route('/{record}/edit'),
        ];
    }
}
