<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Contracts\ChartComponent;
use App\Data\Chart;
use App\Facades\Wallets;
use App\Models\WalletTransaction;
use App\Queries\GrowthInterval;
use App\Queries\TransactionsByInterval;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * @mixin \Livewire\Component
 * @mixin ChartComponent
 */
trait IsChart
{
    public function render(): View
    {
        return view('livewire.charts.chart');
    }

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

    /**
     * @return Collection<int, WalletTransaction>
     */
    protected function getTransactions(): Collection
    {
        return WalletTransaction::query()
            ->with('category')
            ->tap(new TransactionsByInterval($this->interval))
            ->where('wallet_id', Wallets::current()->id)
            ->tap(function (Builder $query): Builder {
                // @phpstan-ignore-next-line
                if (method_exists($this, 'query')) {
                    return $this->query($query);
                }

                return $query;
            })
            ->get();
    }

    protected function getGrowth(): int|float
    {
        return WalletTransaction::query()
            ->where('wallet_id', Wallets::current()->id)
            ->tap(new GrowthInterval($this->interval))
            ->sum('amount') / 100;
    }
}
