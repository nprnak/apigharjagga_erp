<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers\BoqItemsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\ContractorAssignmentsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\InspectionReportsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\MaterialRecordsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\MilestonesRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\ProgressLogsRelationManager;
use App\Filament\Resources\ProjectResource\RelationManagers\SiteVisitsRelationManager;
use App\Models\Client;
use App\Models\Project;
use App\Models\Property;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Project::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'project_name';

    protected static function permissionKey(): string
    {
        return 'projects';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Engineering & Projects';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'property', 'assignedEngineer']);
    }

    /**
     * Site engineers only hold 'projects.log' (not 'projects.manage'), but
     * they still need to open the project record to reach the Site Visits /
     * Progress / Materials / Inspections relation-manager tabs. Each tab's
     * own actions still gate on the specific permission they need.
     */
    public static function canEdit(Model $record): bool
    {
        return static::userHasPermission('projects.manage') || static::userHasPermission('projects.log');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project')
                ->columns(2)
                ->schema([
                    TextInput::make('project_code')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'PRJ-'.Str::upper(Str::random(8)))
                        ->disabled(fn (?Project $record) => $record !== null)
                        ->dehydrated(),
                    TextInput::make('project_name')
                        ->required()
                        ->maxLength(200),
                    Select::make('client_id')
                        ->label('Client')
                        ->options(fn () => Client::pluck('full_name', 'client_id'))
                        ->searchable(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->searchable(),
                    Select::make('assigned_engineer_staff_id')
                        ->label('Assigned Engineer')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    Select::make('status')
                        ->options([
                            'planning' => 'Planning', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold',
                            'completed' => 'Completed', 'cancelled' => 'Cancelled',
                        ])
                        ->default('planning')
                        ->required()
                        ->native(false),
                    DatePicker::make('start_date'),
                    DatePicker::make('expected_end_date'),
                    DatePicker::make('actual_end_date'),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    FileUpload::make('completion_certificate_path')
                        ->label('Completion Certificate')
                        ->disk('public')
                        ->directory('projects/completion-certificates')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project_code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_name')
                    ->label('Project')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('assignedEngineer.full_name')
                    ->label('Engineer')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'info',
                        'on_hold' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d M Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('expected_end_date')
                    ->date('d M Y')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'planning' => 'Planning', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold',
                        'completed' => 'Completed', 'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            MilestonesRelationManager::class,
            SiteVisitsRelationManager::class,
            ProgressLogsRelationManager::class,
            BoqItemsRelationManager::class,
            ContractorAssignmentsRelationManager::class,
            MaterialRecordsRelationManager::class,
            InspectionReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
