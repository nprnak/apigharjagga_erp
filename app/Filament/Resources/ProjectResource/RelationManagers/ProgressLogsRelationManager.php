<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Staff;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ProgressLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'progressLogs';

    protected static ?string $title = 'Progress Log';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) ($user?->hasPermission('projects.log') || $user?->hasPermission('projects.manage'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('log_date')->default(now())->required(),
            Slider::make('percent_complete')
                ->range(0, 100)
                ->default(0),
            Select::make('logged_by_staff_id')
                ->label('Logged By')
                ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                ->searchable(),
            Textarea::make('description')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('log_date')
            ->columns([
                Tables\Columns\TextColumn::make('log_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('percent_complete')->label('% Complete')->suffix('%'),
                Tables\Columns\TextColumn::make('loggedBy.full_name')->label('Logged By')->placeholder('—'),
                Tables\Columns\TextColumn::make('description')->limit(60)->placeholder('—'),
            ])
            ->defaultSort('log_date', 'desc')
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
