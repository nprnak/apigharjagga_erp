<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyRentalResource\Pages;
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
 * A curated, read-only view of properties listed for rent or lease
 * (purpose_of_listing already supports both) — the concrete slice of
 * "rental listing search" this app's data actually supports today.
 * Full lease management (an active tenancy record, paying rent online,
 * requesting maintenance against a specific unit) needs a lease/tenancy
 * model that doesn't exist yet — deliberately not built here; see
 * projectplan.md.
 */
class MyRentalResource extends Resource
{
    protected static ?string $model = PropertyListing::class;

    protected static ?string $navigationLabel = 'Rental Listings';

    protected static ?string $modelLabel = 'Rental Listing';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'application_no';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-key';
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user?->client_type === 'tenant' && $user->hasApprovedKyc();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('purpose_of_listing', ['rent', 'lease'])
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
                    Placeholder::make('rental_amount')
                        ->label('Monthly Rent')
                        ->content(fn (?PropertyListing $record) => $record?->rental_amount ? 'Rs. '.number_format((float) $record->rental_amount, 2).' / month' : 'Contact for pricing'),
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
                Tables\Columns\TextColumn::make('rental_amount')
                    ->label('Monthly Rent')
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
            'index' => Pages\ListMyRentals::route('/'),
            'view' => Pages\ViewMyRental::route('/{record}'),
        ];
    }
}
