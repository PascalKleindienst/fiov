<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Data\Chart;
use App\Facades\Wallets;
use App\Livewire\Concerns\IsChart;
use App\Models\WalletTransaction;
use App\Queries\GrowthInterval;
use App\Queries\TransactionsByInterval;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

abstract class ChartComponent extends Component
{
    use IsChart;

    /**
     * @var 'day'|'week'|'month'|'year'
     */
    public string $interval = TransactionsByInterval::WEEK;

    abstract public function chart(): Chart;

    public function render(): View
    {
        return view('livewire.charts.chart');
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
            ->tap($this->query(...))
            ->get();
    }

    /**
     * @param  Builder<WalletTransaction>  $query
     * @return Builder<WalletTransaction>
     */
    protected function query(Builder $query): Builder
    {
        return $query;
    }

    protected function getGrowth(): int|float
    {
        return WalletTransaction::query()
            ->where('wallet_id', Wallets::current()->id)
            ->tap(new GrowthInterval($this->interval))
            ->sum('amount') / 100;
    }
}
