<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Staff;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InspectionReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'inspectionReports';

    protected static ?string $title = 'Inspection Reports';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) ($user?->hasPermission('projects.log') || $user?->hasPermission('projects.manage'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('inspection_date')->default(now())->required(),
            Select::make('inspected_by_staff_id')
                ->label('Inspected By')
                ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                ->searchable(),
            Select::make('result')
                ->options(['pass' => 'Pass', 'fail' => 'Fail', 'needs_correction' => 'Needs Correction'])
                ->default('pass')
                ->required()
                ->native(false),
            Textarea::make('findings')->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('inspection_date')
            ->columns([
                Tables\Columns\TextColumn::make('inspection_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('inspectedBy.full_name')->label('Inspected By')->placeholder('—'),
                Tables\Columns\TextColumn::make('result')->badge()->color(fn (string $state) => match ($state) {
                    'pass' => 'success', 'fail' => 'danger', default => 'warning',
                }),
                Tables\Columns\TextColumn::make('findings')->limit(60)->placeholder('—'),
            ])
            ->defaultSort('inspection_date', 'desc')
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
