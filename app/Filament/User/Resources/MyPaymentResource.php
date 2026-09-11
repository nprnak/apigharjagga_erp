<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyPaymentResource\Pages;
use App\Models\PaymentReceipt;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Read-only view of the Annex-I payment receipts issued to a Buyer or
 * Owner. Receipts are issued by Finance at the counter, not self-service —
 * this resource exists so a client can see their own payment history, per
 * the "View Receipts" row of the RBAC matrix.
 */
class MyPaymentResource extends Resource
{
    protected static ?string $model = PaymentReceipt::class;

    protected static ?string $navigationLabel = 'My Payments';

    protected static ?string $modelLabel = 'Payment Receipt';

    protected static ?string $pluralModelLabel = 'My Payments';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'receipt_no';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-banknotes';
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return in_array($user?->client_type, ['owner', 'buyer'], true) && $user->hasApprovedKyc();
    }

    public static function getEloquentQuery(): Builder
    {
        $client = Auth::user()?->resolvedClient();

        $query = parent::getEloquentQuery()->with(['property', 'agreement']);

        if (! $client) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('client_id', $client->client_id)
            ->orderByDesc('receipt_date');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receipt')
                ->columns(2)
                ->schema([
                    Placeholder::make('receipt_no')
                        ->content(fn (?PaymentReceipt $record) => $record?->receipt_no ?? '—'),
                    Placeholder::make('receipt_date')
                        ->content(fn (?PaymentReceipt $record) => $record?->receipt_date?->format('d M Y') ?? '—'),
                    Placeholder::make('amount')
                        ->content(fn (?PaymentReceipt $record) => $record ? 'Rs. '.number_format((float) $record->amount, 2) : '—'),
                    Placeholder::make('purpose')
                        ->content(fn (?PaymentReceipt $record) => $record ? ucwords(str_replace('_', ' ', $record->purpose)) : '—'),
                    Placeholder::make('mode_of_payment')
                        ->label('Mode')
                        ->content(fn (?PaymentReceipt $record) => $record ? ucfirst($record->mode_of_payment) : '—'),
                    Placeholder::make('property.property_code')
                        ->label('Property')
                        ->content(fn (?PaymentReceipt $record) => $record?->property?->property_code ?? '—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('amount')
                    ->money('NPR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mode_of_payment')
                    ->badge(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->defaultSort('receipt_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyPayments::route('/'),
            'view' => Pages\ViewMyPayment::route('/{record}'),
        ];
    }
}
