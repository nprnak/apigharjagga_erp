<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use App\Models\User;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Read-only view of the audit_logs table. Rows are written by application
 * code at the point of change; nothing here can be created, edited, or
 * deleted through the admin panel.
 */
class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $recordTitleAttribute = 'log_id';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-shield-exclamation';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Access Control';
    }

    protected static function currentUserCanView(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('system.audit_logs');
    }

    public static function canViewAny(): bool
    {
        return static::currentUserCanView();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('performedBy');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('entity_type')->label('Entity'),
            TextEntry::make('entity_id')->label('Entity ID'),
            TextEntry::make('action')->badge(),
            TextEntry::make('performedBy.full_name')->label('Performed By')->placeholder('System'),
            TextEntry::make('performed_at')->dateTime('d M Y H:i'),
            TextEntry::make('old_value')->label('Old Value')->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '—')->columnSpanFull(),
            TextEntry::make('new_value')->label('New Value')->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : '—')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_type')
                    ->label('Entity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'insert' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('performedBy.full_name')
                    ->label('Performed By')
                    ->placeholder('System'),
                Tables\Columns\TextColumn::make('performed_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(['insert' => 'Insert', 'update' => 'Update', 'delete' => 'Delete']),
                Tables\Filters\SelectFilter::make('entity_type')
                    ->options(fn () => AuditLog::query()->distinct()->pluck('entity_type', 'entity_type')),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->defaultSort('performed_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
