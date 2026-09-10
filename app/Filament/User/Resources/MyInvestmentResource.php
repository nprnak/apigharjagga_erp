<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyInvestmentResource\Pages;
use App\Models\PropertyListing;
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
 * A curated, read-only view of properties listed for joint investment
 * (purpose_of_listing = 'investment', an option that already existed on
 * the listing wizard) — the concrete slice of "investment opportunities"
 * this app's data actually supports today. Requesting a consultation on
 * any of them reuses the public property detail page's existing inquiry
 * form rather than a new lead-capture mechanism.
 */
class MyInvestmentResource extends Resource
{
    protected static ?string $model = PropertyListing::class;

    protected static ?string $navigationLabel = 'Investment Opportunities';

    protected static ?string $modelLabel = 'Investment Opportunity';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'application_no';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chart-bar-square';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->client_type === 'investor';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('purpose_of_listing', 'investment')
            ->whereHas('property', fn (Builder $q) => $q->where('is_listed', true))
            ->with(['property.address', 'property.photos'])
            ->orderByDesc('listing_id');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Property')
                ->columns(2)
                ->schema([
                    Placeholder::make('property_code')
                        ->label('Property Code')
                        ->content(fn (?PropertyListing $record) => $record?->property?->property_code ?? '—'),
                    Placeholder::make('property_type')
                        ->content(fn (?PropertyListing $record) => $record?->property?->property_type ? ucwords(str_replace('_', ' ', $record->property->property_type)) : '—'),
                    Placeholder::make('area')
                        ->content(fn (?PropertyListing $record) => $record?->property?->area ?? '—'),
                    Placeholder::make('expected_selling_price')
                        ->label('Expected Price')
                        ->content(fn (?PropertyListing $record) => $record?->expected_selling_price ? 'Rs. '.number_format((float) $record->expected_selling_price, 2) : 'Contact for pricing'),
                    Placeholder::make('location')
                        ->content(fn (?PropertyListing $record) => $record?->property?->address
                            ? collect([$record->property->address->municipality, $record->property->address->district])->filter()->implode(', ')
                            : '—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('property.photos.file_ref')
                    ->label('Photo')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(1),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('property.property_type')
                    ->label('Type')
                    ->formatStateUsing(fn (?string $state) => $state ? ucwords(str_replace('_', ' ', $state)) : '—'),
                Tables\Columns\TextColumn::make('property.address.municipality')
                    ->label('Location')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('expected_selling_price')
                    ->label('Expected Price')
                    ->money('NPR')
                    ->placeholder('Contact for pricing'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\Action::make('viewOnMarketplace')
                    ->label('View & Enquire')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (PropertyListing $record) => url("/properties/{$record->listing_id}"))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('listing_id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyInvestments::route('/'),
            'view' => Pages\ViewMyInvestment::route('/{record}'),
        ];
    }
}
