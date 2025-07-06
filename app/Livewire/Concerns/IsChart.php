<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Data\Chart;
use App\Models\WalletTransaction;
use App\Queries\TransactionsByInterval;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

trait IsChart
{
    /**
     * @param  Collection<int, WalletTransaction>  $transactions
     * @param  (callable(WalletTransaction $transaction): (int|float))|null  $accumulator
     */
    public function accumulateTransactions(Collection $transactions, Chart $chart, ?callable $accumulator = null): self
    {
        $data = [];
        $transactions->each(function (WalletTransaction $transaction) use (&$data, $accumulator): void {
            $group = $this->getGroup($transaction->created_at);
            $amount = $accumulator ? $accumulator($transaction) : ((int) $transaction->amount->getAmount()) / 100;

            $data[$group] ??= 0;
            $data[$group] += $amount;
        });

        foreach ($data as $key => $value) {
            $chart->addDataPoint($key, $value);
        }

        return $this;
    }

    public function getGroup(?CarbonImmutable $date): string
    {
        $date ??= CarbonImmutable::now();

        return match ($this->interval) {
            TransactionsByInterval::YEAR => $date->translatedFormat('F Y'),
            TransactionsByInterval::MONTH => $date->translatedFormat('W'),
            default => $date->translatedFormat('j F'),
        };
    }
}
