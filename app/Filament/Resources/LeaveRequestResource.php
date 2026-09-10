<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationLabel = 'Leave Requests';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'leave_request_id';

    protected static function permissionKey(): string
    {
        return 'leave';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calendar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Human Resources';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['staff', 'leaveType']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Leave Request')
                ->columns(2)
                ->schema([
                    Select::make('staff_id')
                        ->label('Staff')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->required()
                        ->searchable(),
                    Select::make('leave_type_id')
                        ->label('Leave Type')
                        ->options(fn () => LeaveType::pluck('name', 'leave_type_id'))
                        ->required()
                        ->searchable(),
                    DatePicker::make('start_date')->required(),
                    DatePicker::make('end_date')->required(),
                    TextInput::make('total_days')
                        ->numeric()
                        ->required(),
                    TextInput::make('reason')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.full_name')
                    ->label('Staff')
                    ->searchable(),
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('end_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('total_days')->label('Days'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->actions([
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LeaveRequest $record) => static::userHasPermission('leave.approve') && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->schema([
                        Select::make('approved_by_staff_id')
                            ->label('Approved By')
                            ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (LeaveRequest $record, array $data): void {
                        $record->update(['status' => 'approved', 'approved_by_staff_id' => $data['approved_by_staff_id']]);
                        Notification::make()->title('Leave request approved')->success()->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LeaveRequest $record) => static::userHasPermission('leave.approve') && $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record): void {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title('Leave request rejected')->danger()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('applied_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
