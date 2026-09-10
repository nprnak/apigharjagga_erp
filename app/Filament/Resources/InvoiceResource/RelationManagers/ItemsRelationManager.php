<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Line Items';

    // $invoice is always this resource's Invoice; getOwnerRecord() is
    // declared Model by Filament's base RelationManager class.
    private static function recalculateTotals(Model $invoice): void
    {
        $subtotal = $invoice->items()->sum('amount');
        $invoice->update([
            'subtotal' => $subtotal,
            'total_amount' => $subtotal + $invoice->tax_amount,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('description')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TextInput::make('quantity')
                ->numeric()
                ->default(1)
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('amount', (float) $get('quantity') * (float) $get('unit_price'))),
            TextInput::make('unit_price')
                ->numeric()
                ->prefix('Rs.')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Get $get, Set $set) => $set('amount', (float) $get('quantity') * (float) $get('unit_price'))),
            TextInput::make('amount')
                ->numeric()
                ->prefix('Rs.')
                ->required()
                ->helperText('Auto-calculated as quantity × unit price; adjust if needed.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('unit_price')->money('NPR'),
                Tables\Columns\TextColumn::make('amount')->money('NPR'),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->after(fn () => self::recalculateTotals($this->getOwnerRecord())),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->after(fn () => self::recalculateTotals($this->getOwnerRecord())),
                Actions\DeleteAction::make()
                    ->after(fn () => self::recalculateTotals($this->getOwnerRecord())),
            ]);
    }
}
