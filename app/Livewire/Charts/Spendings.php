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
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class Spendings extends Component implements ChartComponent
{
    use IsChart;

    public string $interval = TransactionsByInterval::MONTH;

    #[Computed]
    public function chart(): Chart
    {
        $chart = new Chart(__('charts.spendings'), Wallets::current()->currency->value, previousTotal: $this->getGrowth(), options: $this->getChartOptions());

        $categories = collect();
        $data = [];
        $this->getTransactions()->where('is_spending', true)->each(function (WalletTransaction $transaction) use (&$data, &$categories): void {
            $categories[$transaction->category->title] = $transaction->category->color?->rgb();

            $group = $this->getGroup($transaction->created_at);

            $data[$group][$transaction->category->title] ??= 0;
            $data[$group][$transaction->category->title] += ((int) $transaction->amount->getAmount() * -1) / 100;
        });

        $chart->addOption('xaxis', [
            'categories' => array_keys($data),
        ]);

        $categories->each(static function (?string $color, string $category) use ($chart, $data): void {
            $color ??= Color::Blue->rgb();
            $chart->addColor($color);

            foreach ($data as $value) {
                $chart->addSeries($category, [$value[$category] ?? 0]);
            }
        });

        return $chart;
    }

    /**
     * @return Collection<string, mixed>
     */
    private function getChartOptions(): Collection
    {
        return collect([
            'chart' => [
                'stacked' => true,
                'height' => 700,
            ],
            'plotOptions' => [
                'bar' => [
                    'distributed' => false,
                    'dataLabels' => [
                        'total' => [
                            'enabled' => true,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
