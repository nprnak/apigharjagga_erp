<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\Staff;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SiteVisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'siteVisits';

    protected static ?string $title = 'Site Visits';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) ($user?->hasPermission('projects.log') || $user?->hasPermission('projects.manage'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('visit_date')->default(now())->required(),
            Select::make('visited_by_staff_id')
                ->label('Visited By')
                ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                ->searchable(),
            Textarea::make('notes')->columnSpanFull(),
            FileUpload::make('photo_paths')
                ->label('Photos')
                ->multiple()
                ->image()
                ->disk('public')
                ->directory('projects/site-visits')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('visit_date')
            ->columns([
                Tables\Columns\TextColumn::make('visit_date')->date('d M Y'),
                Tables\Columns\TextColumn::make('visitedBy.full_name')->label('Visited By')->placeholder('—'),
                Tables\Columns\TextColumn::make('notes')->limit(60)->placeholder('—'),
            ])
            ->defaultSort('visit_date', 'desc')
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
