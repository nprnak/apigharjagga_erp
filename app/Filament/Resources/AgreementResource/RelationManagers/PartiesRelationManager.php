<?php

namespace App\Filament\Resources\AgreementResource\RelationManagers;

use App\Models\Client;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PartiesRelationManager extends RelationManager
{
    protected static string $relationship = 'parties';

    protected static ?string $title = 'Parties';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('agreements.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('party_role')
                ->options([
                    'seller' => 'Seller',
                    'buyer' => 'Buyer',
                    'property_owner' => 'Property Owner',
                    'company' => 'Company (Api Ghar Jagga)',
                ])
                ->required()
                ->live(),
            Select::make('client_id')
                ->label('Client')
                ->options(fn () => Client::pluck('full_name', 'client_id'))
                ->searchable()
                ->visible(fn ($get) => $get('party_role') !== 'company')
                ->required(fn ($get) => $get('party_role') !== 'company'),
            TextInput::make('representative_name'),
            TextInput::make('designation'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('party_role')
            ->columns([
                Tables\Columns\TextColumn::make('party_role')->badge(),
                Tables\Columns\TextColumn::make('client.full_name')->label('Client')->placeholder('—'),
                Tables\Columns\TextColumn::make('representative_name')->placeholder('—'),
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
