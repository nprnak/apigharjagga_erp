<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationLabel = 'Blog / News';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    protected static function permissionKey(): string
    {
        return 'content';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-newspaper';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('author');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->maxLength(200)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set, ?BlogPost $record) => $record === null ? $set('slug', Str::slug($state)) : null),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(220)
                        ->unique(ignoreRecord: true),
                    TextInput::make('category')->maxLength(100),
                    Toggle::make('is_published'),
                    DateTimePicker::make('published_at'),
                    TextInput::make('excerpt')->maxLength(300)->columnSpanFull(),
                    FileUpload::make('cover_image_path')
                        ->image()
                        ->disk('public')
                        ->directory('cms/blog')
                        ->columnSpanFull(),
                    RichEditor::make('content')->required()->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge()->placeholder('—'),
                Tables\Columns\TextColumn::make('author.full_name')->label('Author')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->dateTime('d M Y')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
