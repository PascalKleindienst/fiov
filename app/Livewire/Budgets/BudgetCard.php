<?php

declare(strict_types=1);

namespace App\Livewire\Budgets;

use App\Enums\BudgetStatus;
use App\Models\Budget;
use Flux\Flux;
use Livewire\Component;

final class BudgetCard extends Component
{
    public Budget $budget;

    public function pause(): void
    {
        $this->budget->update(['status' => BudgetStatus::Paused]);
    }

    public function resume(): void
    {
        $this->budget->update(['status' => BudgetStatus::Active]);
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->budget);

        $this->budget->delete();

        Flux::toast(__('budgets.deleted', ['name' => $this->budget->title]));
        Flux::modal('confirm-budget-deletion-'.$this->budget->id)->close();
    }
}
