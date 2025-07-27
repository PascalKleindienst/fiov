<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\Chart;
use Illuminate\Contracts\View\View;

interface ChartComponent
{
    public function chart(): Chart;

    public function render(): View;
}
