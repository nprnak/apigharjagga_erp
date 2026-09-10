<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ComplaintResource\Pages;
use App\Models\Complaint;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ComplaintResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Complaint::class;

    protected static ?string $recordTitleAttribute = 'complaint_code';

    protected static function permissionKey(): string
    {
        return 'complaints';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-exclamation-triangle';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Complaints';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::whereIn('status', ['registered', 'under_investigation'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'property']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Complaint')
                ->columns(2)
                ->schema([
                    Textarea::make('description')
                        ->columnSpanFull()
                        ->rows(3)
                        ->disabled(),
                    Select::make('priority')
                        ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'])
                        ->required(),
                    Select::make('status')
                        ->options([
                            'registered' => 'Registered',
                            'under_investigation' => 'Under Investigation',
                            'pending_customer_response' => 'Pending Customer Response',
                            'resolved' => 'Resolved',
                            'closed' => 'Closed',
                        ])
                        ->required(),
                ]),
            Section::make('Assignment & Resolution')
                ->columns(2)
                ->schema([
                    Select::make('assigned_officer_staff_id')
                        ->label('Assigned Officer')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->live()
                        ->afterStateUpdated(fn ($state, $set) => $set('assigned_officer_name', Staff::find($state)?->full_name))
                        ->searchable(),
                    DatePicker::make('investigation_date'),
                    Textarea::make('findings')
                        ->columnSpanFull()
                        ->rows(2),
                    Textarea::make('corrective_action_taken')
                        ->columnSpanFull()
                        ->rows(2),
                    DatePicker::make('resolution_date'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('complaint_code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('category')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'resolved', 'closed' => 'success',
                        'under_investigation', 'pending_customer_response' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assigned_officer_name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('complaint_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'registered' => 'Registered',
                        'under_investigation' => 'Under Investigation',
                        'pending_customer_response' => 'Pending Customer Response',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']),
            ])
            ->actions([
                Actions\Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (Complaint $record) => static::userCan('assign'))
                    ->schema([
                        Select::make('assigned_officer_staff_id')
                            ->label('Officer')
                            ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (Complaint $record, array $data): void {
                        $record->update([
                            'assigned_officer_staff_id' => $data['assigned_officer_staff_id'],
                            'assigned_officer_name' => Staff::find($data['assigned_officer_staff_id'])?->full_name,
                            'status' => $record->status === 'registered' ? 'under_investigation' : $record->status,
                        ]);

                        Notification::make()->title('Complaint assigned')->success()->send();
                    }),
                Actions\Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Complaint $record) => static::userCan('resolve') && ! in_array($record->status, ['resolved', 'closed'], true))
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('corrective_action_taken')
                            ->label('Corrective Action Taken')
                            ->required(),
                    ])
                    ->action(function (Complaint $record, array $data): void {
                        $record->update([
                            'corrective_action_taken' => $data['corrective_action_taken'],
                            'status' => 'resolved',
                            'resolution_date' => now(),
                        ]);

                        Notification::make()->title('Complaint resolved')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('complaint_date', 'desc');
    }

    protected static function userCan(string $action): bool
    {
        return static::userHasPermission(static::permissionKey().'.'.$action);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
        ];
    }
}
