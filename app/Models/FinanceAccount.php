<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Chart of accounts. account_type drives which report an account appears
 * in: asset/liability/equity feed the Balance Sheet, income/expense feed
 * the Profit & Loss.
 */
class FinanceAccount extends Model
{
    protected $primaryKey = 'account_id';

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const TYPES_DEBIT_NORMAL = ['asset', 'expense'];

    public const TYPES_CREDIT_NORMAL = ['liability', 'equity', 'income'];

    public const CODE_CASH = '1000';

    public const CODE_ACCOUNTS_PAYABLE = '2000';

    public const CODE_OWNERS_EQUITY = '3000';

    /**
     * Maps payment_receipts.purpose to the income account it posts to.
     * Keep in sync with FinanceAccountsSeeder and PaymentReceiptObserver.
     */
    public static function incomeAccountCodeForPurpose(string $purpose): string
    {
        return match ($purpose) {
            'property_valuation_fee' => '4010',
            'field_visit_charge' => '4020',
            'digital_marketing_charge' => '4030',
            'preliminary_consultation_charge' => '4040',
            'property_registration_service' => '4050',
            'brokerage_commission' => '4060',
            default => '4090',
        };
    }

    public function lines(): HasMany
    {
        return $this->hasMany(FinanceTransactionLine::class, 'account_id', 'account_id');
    }

    /**
     * Assets and expenses grow with a debit; liabilities, equity, and
     * income grow with a credit. Used to sign a balance the "natural" way
     * (a positive number means "more of what this account normally holds").
     */
    public function normalBalance(): string
    {
        return in_array($this->account_type, self::TYPES_DEBIT_NORMAL, true) ? 'debit' : 'credit';
    }

    /**
     * Net balance as of a date (inclusive), signed so a positive number
     * always means "more of what this account normally holds" regardless
     * of type.
     */
    public function balanceAsOf(?string $asOfDate = null): float
    {
        $query = $this->lines();

        if ($asOfDate) {
            $query->whereHas('transaction', fn ($q) => $q->where('transaction_date', '<=', $asOfDate));
        }

        $totals = $query->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')->first();

        $net = (float) $totals->total_debit - (float) $totals->total_credit;

        return $this->normalBalance() === 'debit' ? $net : -$net;
    }

    /**
     * Net movement strictly between two dates (inclusive) — used for
     * period reports (P&L, Cash Book) rather than running balances.
     */
    public function movementBetween(string $fromDate, string $toDate): float
    {
        $totals = $this->lines()
            ->whereHas('transaction', fn ($q) => $q->whereBetween('transaction_date', [$fromDate, $toDate]))
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $net = (float) $totals->total_debit - (float) $totals->total_credit;

        return $this->normalBalance() === 'debit' ? $net : -$net;
    }
}
