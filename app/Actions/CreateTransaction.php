<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Icon;
use App\Enums\RecurringFrequency;
use App\Events\TransactionCreatedEvent;
use App\Models\RecurringTransaction;
use App\Models\WalletTransaction;
use Cknow\Money\Money;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateTransaction
{
    /**
     * @param  array{
     *     wallet_id: int,
     *     wallet_category_id: int,
     *     title: string,
     *     icon: Icon|null,
     *     amount: Money|float,
     *     currency: string,
     *     is_investment: bool
     * }  $data
     * @param  array{is_recurring?: bool, recurring_frequency?: string, recurring_end_date?: string}  $recurring
     *
     * @throws Throwable if the transaction fails
     */
    public function handle(array $data, array $recurring = []): void
    {
        DB::transaction(static function () use ($data, $recurring): void {
            $transaction = WalletTransaction::create($data);

            if ($recurring['is_recurring'] ?? false) {
                RecurringTransaction::create([
                    'user_id' => $transaction->wallet->user_id,
                    'wallet_id' => $transaction->wallet_id,
                    'wallet_category_id' => $transaction->wallet_category_id,
                    'title' => $transaction->title,
                    'icon' => $transaction->icon,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'is_investment' => $transaction->is_investment,
                    'frequency' => $recurring['recurring_frequency'] ?? RecurringFrequency::DAILY->value,
                    'start_date' => now(),
                    'end_date' => $recurring['recurring_end_date'] ?? null,
                    'is_active' => true,
                ]);
            }

            TransactionCreatedEvent::dispatch($transaction);
        });
    }
}
