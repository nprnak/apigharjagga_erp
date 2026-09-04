<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PaymentReceiptResource\Pages;
use App\Models\Agreement;
use App\Models\Client;
use App\Models\PaymentReceipt;
use App\Models\Property;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PaymentReceiptResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PaymentReceipt::class;

    protected static ?string $navigationLabel = 'Payment Receipts';

    protected static ?int $navigationSort = 3;

    protected static function permissionKey(): string
    {
        return 'payments';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-banknotes';
    }

    public static function getNavigationGroup(): string|null
    {
        return 'Service Delivery';
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['client', 'agreement', 'property']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receipt')
                ->columns(2)
                ->schema([
                    TextInput::make('receipt_no')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'PR-'.Str::upper(Str::random(8))),
                    DatePicker::make('receipt_date'),
                    Select::make('client_id')
                        ->label('Client')
                        ->options(fn () => Client::pluck('full_name', 'client_id'))
                        ->required()
                        ->searchable(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->searchable(),
                    Select::make('agreement_id')
                        ->label('Agreement')
                        ->options(fn () => Agreement::all()->mapWithKeys(
                            fn (Agreement $a) => [$a->agreement_id => "#{$a->agreement_id} — ".ucfirst(str_replace('_', ' ', $a->agreement_type))]
                        ))
                        ->searchable(),
                    TextInput::make('amount')
                        ->numeric()
                        ->required(),
                    Select::make('purpose')
                        ->options([
                            'property_valuation_fee' => 'Property Valuation Fee',
                            'field_visit_charge' => 'Field Visit Charge',
                            'digital_marketing_charge' => 'Digital Marketing Charge',
                            'preliminary_consultation_charge' => 'Preliminary Consultation Charge',
                            'property_registration_service' => 'Property Registration Service',
                            'brokerage_commission' => 'Brokerage Commission',
                            'other' => 'Other',
                        ])
                        ->required(),
                    Select::make('mode_of_payment')
                        ->options(['cash' => 'Cash', 'cheque' => 'Cheque'])
                        ->live()
                        ->required(),
                    TextInput::make('cheque_no')
                        ->visible(fn ($get) => $get('mode_of_payment') === 'cheque')
                        ->required(fn ($get) => $get('mode_of_payment') === 'cheque'),
                    TextInput::make('bank_name')
                        ->visible(fn ($get) => $get('mode_of_payment') === 'cheque')
                        ->required(fn ($get) => $get('mode_of_payment') === 'cheque'),
                    DatePicker::make('cheque_date')
                        ->visible(fn ($get) => $get('mode_of_payment') === 'cheque'),
                    Select::make('received_by_staff_id')
                        ->label('Received By')
                        ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                        ->searchable(),
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
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client'),
                Tables\Columns\TextColumn::make('purpose')
                    ->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('NPR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mode_of_payment')
                    ->badge(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('purpose')
                    ->options([
                        'property_valuation_fee' => 'Property Valuation Fee',
                        'field_visit_charge' => 'Field Visit Charge',
                        'digital_marketing_charge' => 'Digital Marketing Charge',
                        'preliminary_consultation_charge' => 'Preliminary Consultation Charge',
                        'property_registration_service' => 'Property Registration Service',
                        'brokerage_commission' => 'Brokerage Commission',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('mode_of_payment')
                    ->options(['cash' => 'Cash', 'cheque' => 'Cheque']),
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->defaultSort('receipt_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentReceipts::route('/'),
            'create' => Pages\CreatePaymentReceipt::route('/create'),
            'edit' => Pages\EditPaymentReceipt::route('/{record}/edit'),
        ];
    }
}
