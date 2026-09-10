<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesViaRole;
use App\Filament\Resources\PaymentVoucherResource\Pages;
use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\PaymentVoucher;
use App\Models\Staff;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PaymentVoucherResource extends Resource
{
    use AuthorizesViaRole;

    protected static ?string $model = PaymentVoucher::class;

    protected static ?string $navigationLabel = 'Payment Vouchers';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'voucher_no';

    protected static function permissionKey(): string
    {
        return 'vouchers';
    }

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-receipt-percent';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['account', 'approvedBy']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Voucher')
                ->columns(2)
                ->schema([
                    TextInput::make('voucher_no')
                        ->required()
                        ->maxLength(30)
                        ->default(fn () => 'PV-'.Str::upper(Str::random(8))),
                    DatePicker::make('voucher_date')
                        ->default(now())
                        ->required(),
                    TextInput::make('payee_name')
                        ->required()
                        ->maxLength(150),
                    TextInput::make('purpose')
                        ->required()
                        ->maxLength(255),
                    Select::make('account_id')
                        ->label('Expense Category')
                        ->options(fn () => FinanceAccount::where('account_type', 'expense')->orderBy('account_code')->pluck('account_name', 'account_id'))
                        ->required()
                        ->searchable(),
                    TextInput::make('amount')
                        ->numeric()
                        ->prefix('Rs.')
                        ->required(),
                    Select::make('mode_of_payment')
                        ->options(['cash' => 'Cash', 'cheque' => 'Cheque'])
                        ->default('cash')
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
                    Select::make('status')
                        ->options(['draft' => 'Draft', 'approved' => 'Approved', 'paid' => 'Paid'])
                        ->default('draft')
                        ->disabled()
                        ->dehydrated(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_no')
                    ->label('Voucher')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payee_name')
                    ->label('Payee'),
                Tables\Columns\TextColumn::make('account.account_name')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('NPR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'approved' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('voucher_date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'approved' => 'Approved', 'paid' => 'Paid']),
            ])
            ->actions([
                Actions\Action::make('approveAndPay')
                    ->label('Approve & Post')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('This posts the expense to the ledger (Debit the expense category, Credit Cash & Bank) and cannot be undone from here.')
                    ->visible(fn (PaymentVoucher $record) => static::userHasPermission('vouchers.approve') && $record->status === 'draft')
                    ->schema([
                        Select::make('approved_by_staff_id')
                            ->label('Approved By')
                            ->options(fn () => Staff::where('is_active', true)->pluck('full_name', 'staff_id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (PaymentVoucher $record, array $data): void {
                        $cash = FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();

                        if (! $cash) {
                            Notification::make()->title('Cash & Bank account not found — cannot post')->danger()->send();

                            return;
                        }

                        FinanceTransaction::postBalanced(
                            [
                                'transaction_date' => $record->voucher_date,
                                'reference_type' => 'payment_voucher',
                                'reference_id' => $record->voucher_id,
                                'description' => "Voucher {$record->voucher_no} — {$record->purpose}",
                                'created_by_staff_id' => $data['approved_by_staff_id'],
                            ],
                            [
                                ['account_id' => $record->account_id, 'debit' => (float) $record->amount],
                                ['account_id' => $cash->account_id, 'credit' => (float) $record->amount],
                            ],
                        );

                        $record->update([
                            'status' => 'paid',
                            'approved_by_staff_id' => $data['approved_by_staff_id'],
                        ]);

                        Notification::make()->title('Voucher approved and posted to the ledger')->success()->send();
                    }),
                Actions\EditAction::make()
                    ->visible(fn (PaymentVoucher $record) => $record->status === 'draft'),
            ])
            ->defaultSort('voucher_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentVouchers::route('/'),
            'create' => Pages\CreatePaymentVoucher::route('/create'),
            'edit' => Pages\EditPaymentVoucher::route('/{record}/edit'),
        ];
    }
}
