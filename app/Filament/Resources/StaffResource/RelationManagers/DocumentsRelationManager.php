<?php

namespace App\Filament\Resources\StaffResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'Documents';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('staff.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('document_name')
                ->required()
                ->maxLength(150),
            FileUpload::make('file_path')
                ->disk('public')
                ->directory('staff/documents')
                ->required()
                ->openable()
                ->downloadable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('document_name')
            ->columns([
                Tables\Columns\TextColumn::make('document_name'),
                Tables\Columns\TextColumn::make('uploaded_at')->dateTime('d M Y'),
            ])
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
