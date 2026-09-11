<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyAgreementResource\Pages;
use App\Models\Agreement;
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
 * Read-only view of the Annex-B agreements a Buyer or Owner is party to.
 * Agreements are created at the staff counter via the public /agreement
 * form (ANNEX-B PO-PB / PO-RA), not self-service, so this resource has no
 * create page — it exists purely so a client can track and review what
 * was signed, per the "Review Agreement" / "Track Transaction" rows of the
 * RBAC matrix.
 */
class MyAgreementResource extends Resource
{
    protected static ?string $model = Agreement::class;

    protected static ?string $navigationLabel = 'My Agreements';

    protected static ?string $modelLabel = 'Agreement';

    protected static ?string $pluralModelLabel = 'My Agreements';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'agreement_id';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
    }

    /**
     * Only Owner/Buyer accounts are party to agreements today (per the RBAC
     * matrix); also requires a resolvable Client match (see
     * User::resolvedClient()) since agreements hang off Client, not User.
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return in_array($user?->client_type, ['owner', 'buyer'], true) && $user->hasApprovedKyc();
    }

    public static function getEloquentQuery(): Builder
    {
        $client = Auth::user()?->resolvedClient();

        $query = parent::getEloquentQuery()->with(['property']);

        if (! $client) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereHas('parties', fn (Builder $q) => $q->where('client_id', $client->client_id))
            ->orderByDesc('agreement_date');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Agreement')
                ->columns(2)
                ->schema([
                    Placeholder::make('agreement_type')
                        ->label('Type')
                        ->content(fn (?Agreement $record) => $record?->agreement_type === 'sale_purchase' ? 'Sale / Purchase' : 'Listing / Brokerage'),
                    Placeholder::make('status')
                        ->content(fn (?Agreement $record) => ucfirst($record?->status ?? '—')),
                    Placeholder::make('property.property_code')
                        ->label('Property')
                        ->content(fn (?Agreement $record) => $record?->property?->property_code ?? '—'),
                    Placeholder::make('agreement_date')
                        ->content(fn (?Agreement $record) => $record?->agreement_date?->format('d M Y') ?? '—'),
                    Placeholder::make('total_price')
                        ->content(fn (?Agreement $record) => $record?->total_price ? 'Rs. '.number_format((float) $record->total_price, 2) : '—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agreement_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'sale_purchase' ? 'Sale/Purchase' : 'Brokerage'),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('NPR')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'draft' => 'gray',
                        'terminated', 'breached' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('agreement_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->defaultSort('agreement_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyAgreements::route('/'),
            'view' => Pages\ViewMyAgreement::route('/{record}'),
        ];
    }
}
