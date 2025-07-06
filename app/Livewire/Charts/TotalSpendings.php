<?php

declare(strict_types=1);

namespace App\Livewire\Charts;

use App\Data\Chart;
use App\Enums\Color;
use App\Facades\Wallets;
use App\Models\WalletTransaction;
use Livewire\Attributes\Computed;

final class TotalSpendings extends ChartComponent
{
    #[Computed]
    public function chart(): Chart
    {
        $dataPoint = [];

        $this->getTransactions()->where('is_spending', true)->each(static function (WalletTransaction $transaction) use (&$dataPoint): void {
            $dataPoint[$transaction->category->id] ??= [
                'x' => $transaction->category->title,
                'y' => 0,
                'color' => $transaction->category->color?->rgb() ?? Color::Blue->rgb(),
            ];
            $dataPoint[$transaction->category->id]['y'] += ((int) $transaction->amount->getAmount() * -1) / 100;
        });

        return new Chart(__('charts.total_spendings'), Wallets::current()->currency->value, $dataPoint, $this->getGrowth());
    }
}
