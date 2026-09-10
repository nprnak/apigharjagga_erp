<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BoqItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'boqItems';

    protected static ?string $title = 'Bill of Quantities';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('projects.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('item_description')->required()->maxLength(255)->columnSpanFull(),
            TextInput::make('unit')->maxLength(30),
            TextInput::make('quantity')
                ->numeric()
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('amount', (float) $get('quantity') * (float) $get('rate'))),
            TextInput::make('rate')
                ->numeric()
                ->prefix('Rs.')
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('amount', (float) $get('quantity') * (float) $get('rate'))),
            TextInput::make('amount')->numeric()->prefix('Rs.')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_description')
            ->columns([
                Tables\Columns\TextColumn::make('item_description')->label('Description'),
                Tables\Columns\TextColumn::make('unit')->placeholder('—'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('rate')->money('NPR'),
                Tables\Columns\TextColumn::make('amount')->money('NPR'),
            ])
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
