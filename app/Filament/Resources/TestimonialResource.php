<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Testimonial::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'client_name';

    protected static function permissionKey(): string
    {
        return 'content';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chat-bubble-left-right';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Testimonial')
                ->columns(2)
                ->schema([
                    TextInput::make('client_name')->required()->maxLength(150),
                    TextInput::make('client_role')->maxLength(150),
                    Select::make('rating')
                        ->options([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'])
                        ->default(5)
                        ->required()
                        ->native(false),
                    TextInput::make('display_order')->numeric()->default(0),
                    FileUpload::make('client_photo_path')
                        ->image()
                        ->disk('public')
                        ->directory('cms/testimonials')
                        ->columnSpanFull(),
                    Textarea::make('message')->required()->columnSpanFull(),
                    Toggle::make('is_featured'),
                    Toggle::make('is_active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client_role')->placeholder('—'),
                Tables\Columns\TextColumn::make('rating')->badge(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->defaultSort('display_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
