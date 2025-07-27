<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Contracts\ChartComponent;
use App\Data\Chart;
use App\Enums\Color;
use App\Facades\Wallets;
use App\Livewire\Concerns\IsChart;
use App\Models\WalletTransaction;
use App\Queries\TransactionsByInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Investments extends Component implements ChartComponent
{
    use IsChart;

    public string $interval = TransactionsByInterval::YEAR;

    #[Computed]
    public function chart(): Chart
    {
        $chart = (new Chart(
            __('charts.investments'),
            Wallets::current()->currency->value,
            previousTotal: $this->getGrowth(),
            options: $this->getChartOptions()
        ))
            ->addColor(Color::Green->rgb());

        $this->accumulateTransactions(
            $this->getTransactions(),
            $chart,
            static fn (WalletTransaction $transaction): int|float => ((int) $transaction->amount->getAmount() * -1) / 100
        );

        return $chart;
    }

    /**
     * @param  Builder<WalletTransaction>  $query
     * @return Builder<WalletTransaction>
     */
    public function query(Builder $query): Builder
    {
        return $query->where('is_investment', true);
    }

    /**
     * @return Collection<string, mixed>
     */
    private function getChartOptions(): Collection
    {
        return collect([
            'chart' => [
                'type' => 'area',
            ],
            'stroke' => [
                'curve' => 'straight',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'plotOptions' => [
                'line' => [
                    'colors' => [
                        'threshold' => 0,
                        'colorAboveThreshold' => Color::Green->rgb(),
                        'colorBelowThreshold' => Color::Red->rgb(),
                    ],
                ],
            ],
        ]);
    }
}
