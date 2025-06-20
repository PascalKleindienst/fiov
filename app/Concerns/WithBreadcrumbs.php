<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Data\BreadcrumbItemData;
use Illuminate\Support\Facades\View;

trait WithBreadcrumbs
{
    public function withBreadcrumbs(BreadcrumbItemData ...$breadcrumbs): void
    {
        View::share('breadcrumbs', $breadcrumbs);
    }
}
