<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FinanceTransaction extends Model
{
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
        'created_by_staff_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(FinanceTransactionLine::class, 'transaction_id', 'transaction_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id', 'staff_id');
    }

    /**
     * Creates a transaction with its lines inside one DB transaction,
     * refusing to post if the lines don't balance. This is the only
     * supported way to record a double-entry posting — every automatic
     * posting (receipts, vouchers) and every manual journal entry goes
     * through here so the books can never go out of balance.
     *
     * @param  array<int, array{account_id: int, debit?: float, credit?: float}>  $lines
     */
    public static function postBalanced(array $attributes, array $lines): self
    {
        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new RuntimeException("Unbalanced transaction: debits ({$totalDebit}) != credits ({$totalCredit}).");
        }

        if (count($lines) < 2) {
            throw new RuntimeException('A transaction needs at least two lines.');
        }

        return DB::transaction(function () use ($attributes, $lines) {
            $transaction = self::create($attributes);

            foreach ($lines as $line) {
                $transaction->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                ]);
            }

            return $transaction;
        });
    }
}
