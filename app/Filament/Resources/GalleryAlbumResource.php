<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\GalleryAlbumResource\Pages;
use App\Filament\Resources\GalleryAlbumResource\RelationManagers\ImagesRelationManager;
use App\Models\GalleryAlbum;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryAlbumResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = GalleryAlbum::class;

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    protected static function permissionKey(): string
    {
        return 'content';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-photo';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Album')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required()->maxLength(200),
                    Toggle::make('is_active')->default(true),
                    Textarea::make('description')->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->image()
                        ->disk('public')
                        ->directory('cms/gallery')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_path')->disk('public'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('images_count')
                    ->label('Photos')
                    ->counts('images'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryAlbums::route('/'),
            'create' => Pages\CreateGalleryAlbum::route('/create'),
            'edit' => Pages\EditGalleryAlbum::route('/{record}/edit'),
        ];
    }
}
