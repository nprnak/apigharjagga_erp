<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers\ItemsRelationManager;
use App\Models\Agreement;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class InvoiceResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = Invoice::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'invoice_no';

    protected static function permissionKey(): string
    {
        return 'invoices';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-currency-dollar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'property']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Invoice')
                ->columns(2)
                ->schema([
                    TextInput::make('invoice_no')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'INV-'.Str::upper(Str::random(8)))
                        ->disabled(fn (?Invoice $record) => $record !== null)
                        ->dehydrated(),
                    Select::make('status')
                        ->options([
                            'draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid',
                            'overdue' => 'Overdue', 'cancelled' => 'Cancelled',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),
                    Select::make('client_id')
                        ->label('Client')
                        ->options(fn () => Client::pluck('full_name', 'client_id'))
                        ->searchable(),
                    Select::make('property_id')
                        ->label('Property')
                        ->options(fn () => Property::pluck('property_code', 'property_id'))
                        ->searchable(),
                    Select::make('agreement_id')
                        ->label('Agreement')
                        ->options(fn () => Agreement::all()->mapWithKeys(
                            fn (Agreement $a) => [$a->agreement_id => '#'.$a->agreement_id.' — '.ucfirst(str_replace('_', ' ', $a->agreement_type))]
                        ))
                        ->searchable(),
                    DatePicker::make('issue_date')
                        ->default(now())
                        ->required(),
                    DatePicker::make('due_date'),
                    TextInput::make('tax_amount')
                        ->numeric()
                        ->prefix('Rs.')
                        ->default(0),
                    Textarea::make('notes')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label('Invoice')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('NPR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'sent' => 'info',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('d M Y')
                    ->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft', 'sent' => 'Sent', 'paid' => 'Paid',
                        'overdue' => 'Overdue', 'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Invoice $record) => response()->streamDownload(
                        fn () => print (Pdf::loadView('pdf.invoice', ['invoice' => $record->load('items')])->output()),
                        "{$record->invoice_no}.pdf",
                    )),
                Actions\EditAction::make(),
            ])
            ->defaultSort('issue_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
