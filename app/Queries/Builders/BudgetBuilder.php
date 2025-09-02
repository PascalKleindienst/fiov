<?php

declare(strict_types=1);

namespace App\Queries\Builders;

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Enums\Priority;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TModel of Budget
 *
 * @extends Builder<TModel>
 */
final class BudgetBuilder extends Builder
{
    /**
     * @return self<TModel>
     */
    public function active(): self
    {
        return $this->where('status', BudgetStatus::Active);
    }

    /**
     * @return self<TModel>
     */
    public function default(): self
    {
        return $this->where('type', BudgetType::Default);
    }

    /**
     * @return self<TModel>
     */
    public function goalBased(): self
    {
        return $this->whereIn('type', BudgetType::goalBasedStates());
    }

    /**
     * @return self<TModel>
     */
    public function priority(Priority $priority): self
    {
        return $this->where('priority', $priority);
    }
}
