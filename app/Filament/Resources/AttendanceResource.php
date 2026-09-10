<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Attendance::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'attendance_id';

    protected static function permissionKey(): string
    {
        return 'attendance';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calendar-days';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Human Resources';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('staff');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Attendance')
                ->columns(2)
                ->schema([
                    Select::make('staff_id')
                        ->label('Staff')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->required()
                        ->searchable(),
                    DatePicker::make('attendance_date')
                        ->default(now())
                        ->required(),
                    Select::make('status')
                        ->options([
                            'present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day',
                            'on_leave' => 'On Leave', 'holiday' => 'Holiday',
                        ])
                        ->default('present')
                        ->required()
                        ->native(false),
                    TimePicker::make('check_in'),
                    TimePicker::make('check_out'),
                    TextInput::make('notes')
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
                Tables\Columns\TextColumn::make('attendance_date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'half_day' => 'warning',
                        'on_leave' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('check_in')->time('h:i A')->placeholder('—'),
                Tables\Columns\TextColumn::make('check_out')->time('h:i A')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present', 'absent' => 'Absent', 'half_day' => 'Half Day',
                        'on_leave' => 'On Leave', 'holiday' => 'Holiday',
                    ]),
            ])
            ->defaultSort('attendance_date', 'desc');
    }

    /**
     * Creates a "Present" row for every active staff member not already
     * marked for today. Shared with ListAttendances' header action.
     */
    public static function markTodayAction(): Actions\Action
    {
        return Actions\Action::make('markToday')
            ->label("Mark Today's Attendance")
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->visible(fn () => static::userHasPermission('attendance.manage'))
            ->requiresConfirmation()
            ->modalDescription('Creates a "Present" attendance row for every active staff member not already marked for today. Edit individual rows afterward for anyone absent or on leave.')
            ->action(function (): void {
                $today = now()->toDateString();
                $alreadyMarked = Attendance::where('attendance_date', $today)->pluck('staff_id');
                $toMark = Staff::where('is_active', true)->whereNotIn('staff_id', $alreadyMarked)->get();

                foreach ($toMark as $staff) {
                    Attendance::create([
                        'staff_id' => $staff->staff_id,
                        'attendance_date' => $today,
                        'status' => 'present',
                    ]);
                }

                Notification::make()->title("Marked {$toMark->count()} staff present for today")->success()->send();
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
