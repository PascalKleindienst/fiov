<?php

declare(strict_types=1);

namespace App\Livewire\RecurringTransactions;

use App\Models\RecurringTransaction;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithPagination;

    public function toggleStatus(RecurringTransaction $transaction): void
    {
        $transaction->update([
            'is_active' => ! $transaction->is_active,
        ]);
    }

    public function delete(RecurringTransaction $transaction): void
    {
        $transaction->delete();
    }

    public function render(): View
    {
        return view('livewire.recurring-transactions.index', [
            'transactions' => RecurringTransaction::with(['wallet', 'category'])
                ->latest()
                ->paginate(10),
        ]);
    }
}
