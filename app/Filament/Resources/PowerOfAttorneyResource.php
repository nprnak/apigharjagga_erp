<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PowerOfAttorneyResource\Pages;
use App\Models\PowerOfAttorney;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class PowerOfAttorneyResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PowerOfAttorney::class;

    protected static ?string $navigationLabel = 'Power of Attorney';

    protected static ?string $recordTitleAttribute = 'poa_id';

    protected static function permissionKey(): string
    {
        return 'poa';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Access Control';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['agentUser', 'ownerClient', 'verifiedBy']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Power of Attorney')
                ->columns(2)
                ->schema([
                    Textarea::make('notes')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agentUser.name')
                    ->label('Agent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ownerClient.full_name')
                    ->label('Owner')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('verifiedBy.full_name')
                    ->label('Verified By')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
            ])
            ->actions([
                Actions\Action::make('viewDocument')
                    ->label('View Document')
                    ->icon('heroicon-o-document')
                    ->url(fn (PowerOfAttorney $record) => Storage::disk('public')->url($record->document_path))
                    ->openUrlInNewTab(),
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PowerOfAttorney $record) => static::userHasPermission('poa.manage') && $record->status === 'pending')
                    ->schema([
                        Select::make('verified_by_staff_id')
                            ->label('Verified By')
                            ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (PowerOfAttorney $record, array $data): void {
                        $record->update([
                            'status' => 'approved',
                            'verified_by_staff_id' => $data['verified_by_staff_id'],
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Power of Attorney approved')->success()->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PowerOfAttorney $record) => static::userHasPermission('poa.manage') && $record->status === 'pending')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Reason for rejection')
                            ->required(),
                    ])
                    ->action(function (PowerOfAttorney $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => $data['notes'],
                            'verified_at' => now(),
                        ]);
                        Notification::make()->title('Power of Attorney rejected')->danger()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPowerOfAttorneys::route('/'),
            'edit' => Pages\EditPowerOfAttorney::route('/{record}/edit'),
        ];
    }
}
