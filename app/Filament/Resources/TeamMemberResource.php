<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
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

class TeamMemberResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationLabel = 'Team';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function permissionKey(): string
    {
        return 'content';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-user-group';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team Member')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required()->maxLength(150),
                    TextInput::make('designation')->maxLength(150),
                    TextInput::make('department')->maxLength(100),
                    TextInput::make('display_order')->numeric()->default(0),
                    FileUpload::make('photo_path')
                        ->image()
                        ->disk('public')
                        ->directory('cms/team')
                        ->columnSpanFull(),
                    Textarea::make('bio')->columnSpanFull(),
                    Toggle::make('is_active')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')->disk('public')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('designation')->placeholder('—'),
                Tables\Columns\TextColumn::make('department')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean(),
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
