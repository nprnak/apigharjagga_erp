<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\SiteDocumentResource\Pages;
use App\Models\SiteDocument;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SiteDocumentResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = SiteDocument::class;

    protected static ?string $navigationLabel = 'Document Downloads';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    protected static function permissionKey(): string
    {
        return 'site_documents';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-arrow-down-tray';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->required()->maxLength(200),
                    TextInput::make('category')->maxLength(100),
                    Toggle::make('is_public')->default(true),
                    FileUpload::make('file_path')
                        ->required()
                        ->disk('public')
                        ->directory('cms/documents')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->placeholder('—'),
                Tables\Columns\IconColumn::make('is_public')->label('Public')->boolean(),
                Tables\Columns\TextColumn::make('uploaded_at')->dateTime('d M Y'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('uploaded_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteDocuments::route('/'),
            'create' => Pages\CreateSiteDocument::route('/create'),
            'edit' => Pages\EditSiteDocument::route('/{record}/edit'),
        ];
    }
}
