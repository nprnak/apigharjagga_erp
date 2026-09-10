<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationLabel = 'Contact Messages';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static function permissionKey(): string
    {
        return 'contact_messages';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-envelope';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Website Content';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Message')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->disabled(),
                    TextInput::make('email')->disabled(),
                    TextInput::make('phone')->disabled(),
                    TextInput::make('subject')->disabled(),
                    Textarea::make('message')->disabled()->columnSpanFull(),
                    Select::make('status')
                        ->options(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'])
                        ->required()
                        ->native(false),
                    Textarea::make('admin_note')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('subject')->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'closed' => 'success', 'contacted' => 'info', default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed']),
            ])
            ->actions([
                Actions\Action::make('markContacted')
                    ->label('Mark Contacted')
                    ->icon('heroicon-o-phone')
                    ->color('info')
                    ->visible(fn (ContactMessage $record) => static::userHasPermission('contact_messages.manage') && $record->status === 'new')
                    ->action(function (ContactMessage $record): void {
                        $record->update(['status' => 'contacted']);
                        Notification::make()->title('Marked contacted')->success()->send();
                    }),
                Actions\Action::make('markClosed')
                    ->label('Close')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ContactMessage $record) => static::userHasPermission('contact_messages.manage') && $record->status !== 'closed')
                    ->action(function (ContactMessage $record): void {
                        $record->update(['status' => 'closed']);
                        Notification::make()->title('Closed')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
