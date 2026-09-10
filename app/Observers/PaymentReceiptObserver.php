<?php

namespace App\Observers;

use App\Models\FinanceAccount;
use App\Models\FinanceTransaction;
use App\Models\PaymentReceipt;

/**
 * Every Annex-I receipt is real cash received, so it should always show up
 * in the ledger without a separate manual entry. Posts Debit Cash & Bank /
 * Credit the income account matching the receipt's purpose.
 */
class PaymentReceiptObserver
{
    public function created(PaymentReceipt $receipt): void
    {
        $cash = FinanceAccount::where('account_code', FinanceAccount::CODE_CASH)->first();
        $incomeAccount = FinanceAccount::where('account_code', FinanceAccount::incomeAccountCodeForPurpose($receipt->purpose))->first();

        if (! $cash || ! $incomeAccount) {
            // Chart of accounts not seeded yet — don't block receipt creation over it.
            return;
        }

        FinanceTransaction::postBalanced(
            [
                'transaction_date' => $receipt->receipt_date,
                'reference_type' => 'payment_receipt',
                'reference_id' => $receipt->receipt_id,
                'description' => "Receipt {$receipt->receipt_no}",
                'created_by_staff_id' => $receipt->received_by_staff_id,
            ],
            [
                ['account_id' => $cash->account_id, 'debit' => (float) $receipt->amount],
                ['account_id' => $incomeAccount->account_id, 'credit' => (float) $receipt->amount],
            ],
        );
    }
}
