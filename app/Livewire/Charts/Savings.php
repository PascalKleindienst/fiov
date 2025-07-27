<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Contracts\ChartComponent;
use App\Data\Chart;
use App\Enums\Color;
use App\Facades\Wallets;
use App\Livewire\Concerns\IsChart;
use App\Queries\TransactionsByInterval;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Savings extends Component implements ChartComponent
{
    use IsChart;

    public string $interval = TransactionsByInterval::YEAR;

    #[Computed]
    public function chart(): Chart
    {
        $chart = new Chart(
            __('charts.savings'),
            Wallets::current()->currency->value,
            previousTotal: $this->getGrowth(),
            options: $this->getChartOptions()
        );

        $this->accumulateTransactions($this->getTransactions(), $chart);

        return $chart;
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
                        'colorAboveThreshold' => Color::Blue->rgb(),
                        'colorBelowThreshold' => Color::Red->rgb(),
                    ],
                ],
            ],
        ]);
    }
}
