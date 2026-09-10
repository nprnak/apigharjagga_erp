<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MaterialRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'materialRecords';

    protected static ?string $title = 'Material Records';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) ($user?->hasPermission('projects.log') || $user?->hasPermission('projects.manage'));
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('material_name')->required()->maxLength(150),
            TextInput::make('unit')->maxLength(30),
            TextInput::make('quantity')
                ->numeric()
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('total_cost', (float) $get('quantity') * (float) $get('unit_cost'))),
            TextInput::make('unit_cost')
                ->numeric()
                ->prefix('Rs.')
                ->default(0)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('total_cost', (float) $get('quantity') * (float) $get('unit_cost'))),
            TextInput::make('total_cost')->numeric()->prefix('Rs.')->required(),
            TextInput::make('supplier')->maxLength(150),
            DatePicker::make('received_date')->default(now()),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('material_name')
            ->columns([
                Tables\Columns\TextColumn::make('material_name')->label('Material'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit')->placeholder('—'),
                Tables\Columns\TextColumn::make('total_cost')->money('NPR'),
                Tables\Columns\TextColumn::make('supplier')->placeholder('—'),
                Tables\Columns\TextColumn::make('received_date')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('received_date', 'desc')
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
