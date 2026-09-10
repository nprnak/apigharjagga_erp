<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\MyValuationRequestResource\Pages;
use App\Filament\User\Resources\MyValuationRequestResource\RelationManagers\ReportsRelationManager;
use App\Models\Property;
use App\Models\ValuationRequest;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MyValuationRequestResource extends Resource
{
    protected static ?string $model = ValuationRequest::class;

    protected static ?string $navigationLabel = 'My Valuation Requests';

    protected static ?string $modelLabel = 'Valuation Request';

    protected static ?string $pluralModelLabel = 'My Valuation Requests';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'request_code';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-calculator';
    }

    public static function getEloquentQuery(): Builder
    {
        $userId = Auth::id();

        return parent::getEloquentQuery()
            ->whereHas('property', fn (Builder $query) => $query->where('user_id', $userId))
            ->with(['property', 'reports']);
    }

    /**
     * Requesting a valuation on your own property is an Owner action (see
     * the RBAC matrix's "Request Valuation" row — Buyer/Investor/Tenant are
     * not granted it).
     */
    public static function canViewAny(): bool
    {
        return Auth::user()?->client_type === 'owner';
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->client_type === 'owner' && $user->kycVerification?->status === 'approved';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Valuation Request')
                ->description('Submit a valuation request for one of your approved KYC property listings.')
                ->columns(2)
                ->schema([
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::query()
                            ->where('user_id', Auth::id())
                            ->pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    Select::make('purpose_of_valuation')
                        ->label('Purpose')
                        ->options([
                            'bank_loan_mortgage' => 'Bank Loan / Mortgage',
                            'buying_selling' => 'Buying / Selling',
                            'insurance' => 'Insurance',
                            'legal' => 'Legal',
                            'investment_decision' => 'Investment Decision',
                            'other' => 'Other',
                        ])
                        ->required(),
                    Select::make('requested_valuation_type')
                        ->label('Valuation Type')
                        ->options([
                            'market_value' => 'Market Value',
                            'forced_sale_value' => 'Forced Sale Value',
                            'government_value_reference' => 'Government Value Reference',
                            'rental_value' => 'Rental Value',
                        ])
                        ->required(),
                    DatePicker::make('preferred_visit_date')
                        ->label('Preferred Site Visit Date')
                        ->minDate(now()),
                    Select::make('preferred_visit_time')
                        ->label('Preferred Visit Time')
                        ->options([
                            'morning' => 'Morning',
                            'afternoon' => 'Afternoon',
                        ]),
                    Textarea::make('remarks')
                        ->columnSpanFull()
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('request_code')
                    ->label('Request')
                    ->searchable(),
                Tables\Columns\TextColumn::make('property.property_code')
                    ->label('Property'),
                Tables\Columns\TextColumn::make('purpose_of_valuation')
                    ->label('Purpose')
                    ->formatStateUsing(fn (string $state): string => Str::headline(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('requested_valuation_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'report_issued' => 'success',
                        'site_visit_scheduled', 'in_progress' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('preferred_visit_date')
                    ->label('Preferred Visit')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('field_visit_date')
                    ->label('Scheduled Visit')
                    ->date('d M Y')
                    ->placeholder('Not scheduled'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'received' => 'Received',
                        'site_visit_scheduled' => 'Site Visit Scheduled',
                        'in_progress' => 'In Progress',
                        'report_issued' => 'Report Issued',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
            ])
            ->defaultSort('application_received_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ReportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyValuationRequests::route('/'),
            'create' => Pages\CreateMyValuationRequest::route('/create'),
            'view' => Pages\ViewMyValuationRequest::route('/{record}'),
        ];
    }
}
