<?php

namespace App\Filament\Pages;

use App\Models\FinanceAccount;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RevenueReports extends Page
{
    protected string $view = 'filament.pages.revenue-reports';

    protected static ?string $navigationLabel = 'Revenue Reports';

    protected static ?string $title = 'Revenue Reports';

    protected static ?int $navigationSort = 6;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-arrow-trending-up';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::guard('admin')->user();

        return (bool) $user?->hasPermission('finance_reports.view');
    }

    /**
     * @return Collection<int, FinanceAccount>
     */
    protected function incomeAccounts(): Collection
    {
        return FinanceAccount::where('account_type', 'income')->orderBy('account_code')->get();
    }

    public function revenueThisMonth(): float
    {
        $from = now()->startOfMonth()->toDateString();
        $to = now()->toDateString();

        return $this->incomeAccounts()->sum(fn (FinanceAccount $a) => $a->movementBetween($from, $to));
    }

    public function revenueThisYear(): float
    {
        $from = now()->startOfYear()->toDateString();
        $to = now()->toDateString();

        return $this->incomeAccounts()->sum(fn (FinanceAccount $a) => $a->movementBetween($from, $to));
    }

    /**
     * @return array<string, float>
     */
    public function byCategoryThisYear(): array
    {
        $from = now()->startOfYear()->toDateString();
        $to = now()->toDateString();

        return $this->incomeAccounts()
            ->mapWithKeys(fn (FinanceAccount $a) => [$a->account_name => $a->movementBetween($from, $to)])
            ->filter(fn ($amount) => $amount > 0)
            ->sortDesc()
            ->all();
    }

    /**
     * @return array<int, array{month: string, total: float}>
     */
    public function monthlyTrend(): array
    {
        $months = collect(range(5, 0))->map(fn (int $i) => now()->subMonths($i));
        $accounts = $this->incomeAccounts();

        return $months->map(function ($month) use ($accounts) {
            $from = $month->copy()->startOfMonth()->toDateString();
            $to = $month->copy()->endOfMonth()->toDateString();
            $total = $accounts->sum(fn (FinanceAccount $a) => $a->movementBetween($from, $to));

            return ['month' => $month->format('M Y'), 'total' => $total];
        })->all();
    }
}
