<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Data\Chart;
use App\Enums\Color;
use App\Facades\Wallets;
use App\Queries\TransactionsByInterval;
use Livewire\Attributes\Computed;

final class Savings extends ChartComponent
{
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
     * @return array<string, mixed>
     */
    private function getChartOptions(): array
    {
        return [
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
        ];
    }
}
