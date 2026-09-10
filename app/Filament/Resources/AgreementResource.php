<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\AgreementResource\Pages;
use App\Filament\Resources\AgreementResource\RelationManagers\PartiesRelationManager;
use App\Filament\Resources\AgreementResource\RelationManagers\WitnessesRelationManager;
use App\Models\Agreement;
use App\Models\Property;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AgreementResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Agreement::class;

    protected static ?string $navigationLabel = 'Agreements';

    protected static ?string $recordTitleAttribute = 'agreement_id';

    protected static function permissionKey(): string
    {
        return 'agreements';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Service Delivery';
    }

    /**
     * 'agreements.view' (track transaction only) and 'agreements.manage'
     * (create/edit, per the ANNEX-B counter form) both reach this list;
     * canEdit is narrowed separately below.
     */
    public static function canViewAny(): bool
    {
        return static::userHasPermission('agreements.view') || static::userHasPermission('agreements.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return static::userHasPermission('agreements.manage') || static::userHasPermission('agreements.review');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['property']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Agreement')
                ->columns(2)
                ->schema([
                    Select::make('agreement_type')
                        ->options([
                            'sale_purchase' => 'Sale / Purchase (PO-PB)',
                            'listing_brokerage' => 'Listing / Brokerage (PO-RA)',
                        ])
                        ->required()
                        ->live(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->required()
                        ->searchable(),
                    DatePicker::make('agreement_date')->required(),
                    TextInput::make('place'),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'active' => 'Active',
                            'completed' => 'Completed',
                            'terminated' => 'Terminated',
                            'breached' => 'Breached',
                        ])
                        ->required(),
                ]),
            Section::make('Sale / Purchase Terms')
                ->columns(2)
                ->visible(fn ($get) => $get('agreement_type') === 'sale_purchase')
                ->schema([
                    TextInput::make('total_price')->numeric(),
                    TextInput::make('total_price_words'),
                    TextInput::make('advance_payment')->numeric(),
                    TextInput::make('balance_payment')->numeric(),
                    DatePicker::make('final_payment_date'),
                ]),
            Section::make('Brokerage Terms')
                ->columns(2)
                ->visible(fn ($get) => $get('agreement_type') === 'listing_brokerage')
                ->schema([
                    TextInput::make('commission_rate_percent')->numeric()->suffix('%'),
                    TextInput::make('commission_fixed_amount')->numeric(),
                    TextInput::make('agreement_period_months')->numeric(),
                    TextInput::make('termination_notice_days')->numeric(),
                ]),
            Section::make('Property Description')
                ->columns(2)
                ->collapsible()
                ->schema([
                    Textarea::make('house_description')->columnSpanFull(),
                    TextInput::make('boundary_east')->label('Boundary — East'),
                    TextInput::make('boundary_west')->label('Boundary — West'),
                    TextInput::make('boundary_north')->label('Boundary — North'),
                    TextInput::make('boundary_south')->label('Boundary — South'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agreement_id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agreement_type')
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
            ->filters([
                Tables\Filters\SelectFilter::make('agreement_type')
                    ->options([
                        'sale_purchase' => 'Sale/Purchase',
                        'listing_brokerage' => 'Brokerage',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'terminated' => 'Terminated',
                        'breached' => 'Breached',
                    ]),
            ])
            ->actions([
                Actions\Action::make('review')
                    ->label('Mark Reviewed & Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Agreement $record) => static::userHasPermission('agreements.review') && $record->status === 'draft')
                    ->action(function (Agreement $record): void {
                        $record->update(['status' => 'active']);
                        Notification::make()->title('Agreement reviewed and activated')->success()->send();
                    }),
                Actions\EditAction::make(),
            ])
            ->defaultSort('agreement_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PartiesRelationManager::class,
            WitnessesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgreements::route('/'),
            'edit' => Pages\EditAgreement::route('/{record}/edit'),
        ];
    }
}
