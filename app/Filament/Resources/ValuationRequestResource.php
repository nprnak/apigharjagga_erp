<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ValuationRequestResource\Pages;
use App\Filament\Resources\ValuationRequestResource\RelationManagers\ReportsRelationManager;
use App\Models\Staff;
use App\Models\ValuationRequest;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ValuationRequestResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = ValuationRequest::class;

    protected static ?string $navigationLabel = 'Valuation Requests';

    protected static ?string $recordTitleAttribute = 'request_code';

    protected static function permissionKey(): string
    {
        return 'valuations';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calculator';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Valuation & Survey';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'received')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'property', 'assignedValuator']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request Details')
                ->columns(2)
                ->schema([
                    TextInput::make('request_code')
                        ->disabled(),
                    Select::make('status')
                        ->options([
                            'received' => 'Received',
                            'site_visit_scheduled' => 'Site Visit Scheduled',
                            'in_progress' => 'In Progress',
                            'report_issued' => 'Report Issued',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    TextInput::make('purpose_of_valuation')
                        ->disabled(),
                    TextInput::make('requested_valuation_type')
                        ->disabled(),
                ]),
            Section::make('Assignment & Site Visit')
                ->columns(2)
                ->schema([
                    Select::make('assigned_valuator_staff_id')
                        ->label('Assigned Valuer/Surveyor')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
                    DatePicker::make('preferred_visit_date')
                        ->disabled(),
                    DatePicker::make('field_visit_date')
                        ->label('Scheduled Visit Date'),
                    Textarea::make('remarks')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('requested_valuation_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('assignedValuator.full_name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'report_issued' => 'success',
                        'site_visit_scheduled', 'in_progress' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('preferred_visit_date')
                    ->date('d M Y')
                    ->label('Preferred Visit'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'received' => 'Received',
                        'site_visit_scheduled' => 'Site Visit Scheduled',
                        'in_progress' => 'In Progress',
                        'report_issued' => 'Report Issued',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn () => static::userHasPermission('valuations.manage'))
                    ->schema([
                        Select::make('assigned_valuator_staff_id')
                            ->label('Valuer/Surveyor')
                            ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (ValuationRequest $record, array $data): void {
                        $record->update([
                            'assigned_valuator_staff_id' => $data['assigned_valuator_staff_id'],
                        ]);
                        Notification::make()->title('Valuer assigned')->success()->send();
                    }),
                Actions\Action::make('scheduleVisit')
                    ->label('Schedule Visit')
                    ->icon('heroicon-o-calendar-days')
                    ->color('warning')
                    ->visible(fn (ValuationRequest $record) => static::userHasPermission('valuations.manage') && $record->status === 'received')
                    ->schema([
                        DatePicker::make('field_visit_date')
                            ->required(),
                    ])
                    ->action(function (ValuationRequest $record, array $data): void {
                        if (! $record->assigned_valuator_staff_id) {
                            Notification::make()->title('Assign a valuer before scheduling the visit')->danger()->send();

                            return;
                        }

                        $record->update([
                            'field_visit_date' => $data['field_visit_date'],
                            'status' => 'site_visit_scheduled',
                        ]);
                        Notification::make()->title('Site visit scheduled')->success()->send();
                    }),
                Actions\Action::make('startValuation')
                    ->label('Start Valuation')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->visible(fn (ValuationRequest $record) => static::userHasPermission('valuations.manage') && $record->status === 'site_visit_scheduled')
                    ->requiresConfirmation()
                    ->action(function (ValuationRequest $record): void {
                        $record->update(['status' => 'in_progress']);
                        Notification::make()->title('Valuation started')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('application_received_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListValuationRequests::route('/'),
            'edit' => Pages\EditValuationRequest::route('/{record}/edit'),
        ];
    }
}
