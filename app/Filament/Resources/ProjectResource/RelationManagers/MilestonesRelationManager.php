<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Milestones';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('projects.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(200)->columnSpanFull(),
            TextInput::make('sequence')->numeric()->default(0),
            DatePicker::make('due_date'),
            DatePicker::make('completed_date'),
            Select::make('status')
                ->options(['pending' => 'Pending', 'completed' => 'Completed', 'delayed' => 'Delayed'])
                ->default('pending')
                ->required()
                ->native(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('sequence')->label('#'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('due_date')->date('d M Y')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    'completed' => 'success', 'delayed' => 'danger', default => 'gray',
                }),
            ])
            ->defaultSort('sequence')
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
