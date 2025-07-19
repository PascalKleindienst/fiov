<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\RecurringTransaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

final class CreateRecurringTransaction
{
    public function handle(RecurringTransaction $recurringTransaction): void
    {
        DB::transaction(static function () use ($recurringTransaction): void {
            $transaction = new WalletTransaction([
                'title' => $recurringTransaction->title,
                'amount' => $recurringTransaction->amount,
                'currency' => $recurringTransaction->currency,
                'is_investment' => $recurringTransaction->is_investment,
                'icon' => $recurringTransaction->icon,
                'user_id' => $recurringTransaction->user_id,
                'wallet_id' => $recurringTransaction->wallet_id,
                'wallet_category_id' => $recurringTransaction->wallet_category_id,
            ]);

            $transaction->save();

            $recurringTransaction->update([
                'last_processed_at' => now(),
            ]);
        });
    }
}
