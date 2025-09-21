<?php

declare(strict_types=1);

namespace App\Livewire\RecurringTransactions;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Models\RecurringTransaction;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('recurring_transactions.index')]
final class Index extends Component
{
    use WithBreadcrumbs;
    use WithPagination;

    public function toggleStatus(RecurringTransaction $transaction): void
    {
        $transaction->update([
            'is_active' => ! $transaction->is_active,
        ]);
    }

    public function delete(RecurringTransaction $transaction): void
    {
        $this->authorize('delete', $transaction);
        Flux::toast(__('recurring_transactions.deleted', ['name' => $transaction->title]));
        Flux::modal('confirm-deletion-'.$transaction->id)->close();
        $transaction->delete();
    }

    public function render(): View
    {
        $this->withBreadcrumbs(new BreadcrumbItemData(__('recurring_transactions.index')));

        return view('livewire.recurring-transactions.index', [
            'transactions' => RecurringTransaction::with(['wallet', 'category'])
                ->whereHas('wallet')
                ->latest()
                ->paginate(10),
        ]);
    }
}
