<?php

namespace App\Filament\Resources\AgreementResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class WitnessesRelationManager extends RelationManager
{
    protected static string $relationship = 'witnesses';

    protected static ?string $title = 'Witnesses';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('agreements.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('full_name')->required(),
            TextInput::make('citizenship_no'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('citizenship_no')->placeholder('—'),
                Tables\Columns\TextColumn::make('signed_at')->dateTime('d M Y H:i')->placeholder('Not signed'),
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
