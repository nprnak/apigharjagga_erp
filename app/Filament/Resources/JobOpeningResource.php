<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\JobOpeningResource\Pages;
use App\Filament\Resources\JobOpeningResource\RelationManagers\ApplicationsRelationManager;
use App\Models\JobOpening;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class JobOpeningResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = JobOpening::class;

    protected static ?string $navigationLabel = 'Careers';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    protected static function permissionKey(): string
    {
        return 'careers';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-briefcase';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Job Opening')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required()->maxLength(200),
                    TextInput::make('department')->maxLength(100),
                    TextInput::make('location')->maxLength(150),
                    Select::make('employment_type')
                        ->options([
                            'full_time' => 'Full Time', 'part_time' => 'Part Time',
                            'contract' => 'Contract', 'internship' => 'Internship',
                        ])
                        ->default('full_time')
                        ->required()
                        ->native(false),
                    DatePicker::make('posted_date')->default(now()),
                    DatePicker::make('closing_date'),
                    Textarea::make('description')->columnSpanFull(),
                    Textarea::make('requirements')->columnSpanFull(),
                    Toggle::make('is_active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('department')->placeholder('—'),
                Tables\Columns\TextColumn::make('location')->placeholder('—'),
                Tables\Columns\TextColumn::make('employment_type')->badge(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Applicants')
                    ->counts('applications'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
                Tables\Columns\TextColumn::make('closing_date')->date('d M Y')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ApplicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobOpenings::route('/'),
            'create' => Pages\CreateJobOpening::route('/create'),
            'edit' => Pages\EditJobOpening::route('/{record}/edit'),
        ];
    }
}
