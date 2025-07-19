<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CreateRecurringTransaction;
use App\Models\RecurringTransaction;
use App\Queries\TransactionsByLastProcessedAt;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

final class ProcessRecurringTransactionsCommand extends Command
{
    protected $signature = 'transactions:process:recurring';

    protected $description = 'Process recurring transactions';

    public function handle(CreateRecurringTransaction $createAction): int
    {
        $today = now()->startOfDay();

        RecurringTransaction::query()
            ->with(['wallet', 'category'])
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where(static fn (Builder $query) => $query->whereNull('end_date')->orWhere('end_date', '>=', $today))
            ->tap(new TransactionsByLastProcessedAt($today))
            ->each(fn (RecurringTransaction $recurringTransaction) => $this->processTransaction($recurringTransaction,
                $createAction));

        return self::SUCCESS;
    }

    private function processTransaction(
        RecurringTransaction $transaction,
        CreateRecurringTransaction $createRecurringTransaction
    ): void {
        $createRecurringTransaction->handle($transaction);

        $this->info('Processed recurring transaction: '.$transaction->title);
    }
}
