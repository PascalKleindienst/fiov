<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TransactionCreatedEvent;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\WalletCategory;
use Illuminate\Database\Eloquent\Relations\Relation;

final readonly class UpdateBudgetListener
{
    public function handle(TransactionCreatedEvent $event): void
    {
        // Load budgets with categories matching the transaction category
        $budgets = Budget::query()
            ->with([
                'categories' => static function (Relation $query) use ($event): void {
                    /** @var Relation<WalletCategory, Budget, BudgetCategory> $query */
                    $query->where('id', $event->transaction->wallet_category_id);
                },
            ])
            ->whereRelation('categories', 'id', '=', $event->transaction->wallet_category_id)->get();

        if ($budgets->isEmpty()) {
            return;
        }

        // Update budgets amounts for said categories
        foreach ($budgets as $budget) {
            $budgetCategory = $budget->categories->first()?->pivot;

            // should not happen
            if (empty($budgetCategory)) {
                continue;
            }

            // Update used amount
            $currentAmount = $budgetCategory->used_amount ?? money(0);
            $budgetCategory->used_amount = $currentAmount->add($event->transaction->amount);

            $budgetCategory->save();

            // TODO: Noficition if amount > 80%
            // TODO: Noficition if amount > 100%
            // TODO: If goal based -> add milestone
        }
    }
}
