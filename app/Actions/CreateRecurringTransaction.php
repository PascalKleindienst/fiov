<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\RecurringTransaction;
use Illuminate\Support\Facades\DB;

final readonly class CreateRecurringTransaction
{
    public function __construct(private CreateTransaction $createTransaction) {}

    public function handle(RecurringTransaction $recurringTransaction): void
    {
        DB::transaction(function () use ($recurringTransaction): void {
            $this->createTransaction->handle([
                'wallet_id' => $recurringTransaction->wallet_id,
                'wallet_category_id' => $recurringTransaction->wallet_category_id,
                'title' => $recurringTransaction->title,
                'icon' => $recurringTransaction->icon,
                'amount' => $recurringTransaction->amount,
                'currency' => $recurringTransaction->currency,
                'is_investment' => $recurringTransaction->is_investment,
            ]);

            $recurringTransaction->update([
                'last_processed_at' => now(),
            ]);
        });
    }
}
