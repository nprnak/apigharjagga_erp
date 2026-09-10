<?php

namespace App\Filament\Pages;

use App\Models\FinanceAccount;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Cash Book, Ledger, Profit & Loss, and Balance Sheet are all views over
 * the same finance_transaction_lines data (see FinanceAccount for the
 * balance/movement math), so they live on one page sharing one date-range
 * filter instead of four disconnected screens.
 */
class FinanceReports extends Page
{
    protected string $view = 'filament.pages.finance-reports';

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $title = 'Financial Reports';

    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Finance';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('finance_reports.view');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->toDateString(),
            'ledger_account_id' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Report Period')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('from_date')->required()->live(),
                        DatePicker::make('to_date')->required()->live(),
                        Select::make('ledger_account_id')
                            ->label('Ledger Account')
                            ->helperText('Defaults to Cash & Bank if none selected')
                            ->options(fn () => FinanceAccount::orderBy('account_code')->pluck('account_name', 'account_id'))
                            ->searchable()
                            ->live(),
                    ]),
            ]);
    }

    protected function fromDate(): string
    {
        return $this->data['from_date'] ?? now()->startOfMonth()->toDateString();
    }

    protected function toDate(): string
    {
        return $this->data['to_date'] ?? now()->toDateString();
    }

    protected function dayBefore(string $date): string
    {
        return Carbon::parse($date)->subDay()->toDateString();
    }

    protected function cashAccount(): ?FinanceAccount
    {
        return FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();
    }

    /**
     * @return array{opening: float, closing: float, rows: array<int, array<string, mixed>>}
     */
    public function cashBookRows(): array
    {
        return $this->accountLedgerRows($this->cashAccount());
    }

    /**
     * @return array{account: ?FinanceAccount, opening: float, closing: float, rows: array<int, array<string, mixed>>}
     */
    public function ledgerRows(): array
    {
        $accountId = $this->data['ledger_account_id'] ?? null;
        $account = $accountId ? FinanceAccount::find((int) $accountId) : $this->cashAccount();

        return ['account' => $account, ...$this->accountLedgerRows($account)];
    }

    /**
     * @return array{opening: float, closing: float, rows: array<int, array<string, mixed>>}
     */
    protected function accountLedgerRows(?FinanceAccount $account): array
    {
        if (! $account) {
            return ['opening' => 0.0, 'closing' => 0.0, 'rows' => []];
        }

        $opening = $account->balanceAsOf($this->dayBefore($this->fromDate()));
        $normalDebit = $account->normalBalance() === 'debit';

        $lines = $account->lines()
            ->whereHas('transaction', fn ($q) => $q->whereBetween('transaction_date', [$this->fromDate(), $this->toDate()]))
            ->with('transaction')
            ->get()
            ->sortBy(fn ($line) => $line->transaction->transaction_date);

        $running = $opening;
        $rows = [];

        foreach ($lines as $line) {
            $signed = $normalDebit
                ? ((float) $line->debit - (float) $line->credit)
                : ((float) $line->credit - (float) $line->debit);
            $running += $signed;

            $rows[] = [
                'date' => $line->transaction->transaction_date->format('d M Y'),
                'description' => $line->transaction->description,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
                'balance' => $running,
            ];
        }

        return ['opening' => $opening, 'closing' => $running, 'rows' => $rows];
    }

    /**
     * @return array{income: array<int, array{name: string, amount: float}>, expense: array<int, array{name: string, amount: float}>, totalIncome: float, totalExpense: float, netProfit: float}
     */
    public function profitAndLoss(): array
    {
        $income = FinanceAccount::where('account_type', 'income')->orderBy('account_code')->get()
            ->map(fn (FinanceAccount $a) => ['name' => $a->account_name, 'amount' => $a->movementBetween($this->fromDate(), $this->toDate())])
            ->all();

        $expense = FinanceAccount::where('account_type', 'expense')->orderBy('account_code')->get()
            ->map(fn (FinanceAccount $a) => ['name' => $a->account_name, 'amount' => $a->movementBetween($this->fromDate(), $this->toDate())])
            ->all();

        $totalIncome = array_sum(array_column($income, 'amount'));
        $totalExpense = array_sum(array_column($expense, 'amount'));

        return [
            'income' => $income,
            'expense' => $expense,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit' => $totalIncome - $totalExpense,
        ];
    }

    /**
     * As-of the report's "to" date. Retained earnings is computed as
     * cumulative income minus cumulative expense since inception (not just
     * this period) — with balanced double-entry postings this always makes
     * Assets = Liabilities + Equity + Retained Earnings hold exactly.
     *
     * @return array{assets: array<int, array{name: string, amount: float}>, liabilities: array<int, array{name: string, amount: float}>, equity: array<int, array{name: string, amount: float}>, totalAssets: float, totalLiabilities: float, totalEquity: float, retainedEarnings: float}
     */
    public function balanceSheet(): array
    {
        $asOf = $this->toDate();

        $assets = FinanceAccount::where('account_type', 'asset')->orderBy('account_code')->get()
            ->map(fn (FinanceAccount $a) => ['name' => $a->account_name, 'amount' => $a->balanceAsOf($asOf)])
            ->all();

        $liabilities = FinanceAccount::where('account_type', 'liability')->orderBy('account_code')->get()
            ->map(fn (FinanceAccount $a) => ['name' => $a->account_name, 'amount' => $a->balanceAsOf($asOf)])
            ->all();

        $equity = FinanceAccount::where('account_type', 'equity')->orderBy('account_code')->get()
            ->map(fn (FinanceAccount $a) => ['name' => $a->account_name, 'amount' => $a->balanceAsOf($asOf)])
            ->all();

        $totalIncomeToDate = FinanceAccount::where('account_type', 'income')->get()->sum(fn (FinanceAccount $a) => $a->balanceAsOf($asOf));
        $totalExpenseToDate = FinanceAccount::where('account_type', 'expense')->get()->sum(fn (FinanceAccount $a) => $a->balanceAsOf($asOf));
        $retainedEarnings = $totalIncomeToDate - $totalExpenseToDate;

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'totalAssets' => array_sum(array_column($assets, 'amount')),
            'totalLiabilities' => array_sum(array_column($liabilities, 'amount')),
            'totalEquity' => array_sum(array_column($equity, 'amount')) + $retainedEarnings,
            'retainedEarnings' => $retainedEarnings,
        ];
    }
}
