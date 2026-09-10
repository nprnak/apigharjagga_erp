<?php

namespace App\Filament\Resources\GalleryAlbumResource\RelationManagers;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Photos';

    protected static function canManage(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('content.manage');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('image_path')
                ->image()
                ->disk('public')
                ->directory('cms/gallery')
                ->required()
                ->columnSpanFull(),
            TextInput::make('caption')->maxLength(255),
            TextInput::make('display_order')->numeric()->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->disk('public'),
                Tables\Columns\TextColumn::make('caption')->placeholder('—'),
                Tables\Columns\TextColumn::make('display_order'),
            ])
            ->defaultSort('display_order')
            ->headerActions([
                Actions\CreateAction::make()->visible(fn () => static::canManage()),
            ])
            ->recordActions([
                Actions\EditAction::make()->visible(fn () => static::canManage()),
                Actions\DeleteAction::make()->visible(fn () => static::canManage()),
            ]);
    }
}
