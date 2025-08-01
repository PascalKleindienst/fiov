<?php

declare(strict_types=1);

namespace App\Livewire\Transactions;

use App\Facades\Wallets;
use App\Livewire\Forms\TransactionForm;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

final class Create extends Component
{
    public TransactionForm $form;

    public function mount(): void
    {
        abort_if(! Auth::user(), 403);

        $this->form->fill([
            'wallet_id' => Wallets::current()->id,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(): void
    {
        try {
            $this->form->submit();
        } catch (Throwable $throwable) {
            Flux::toast(__('general.error_notification', ['error' => $throwable->getMessage()]), duration: null, variant: 'danger');

            if ($throwable instanceof ValidationException) {
                throw $throwable;
            }

            return;
        }

        Flux::toast(__('general.changes_have_been_saved'), variant: 'success');

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): View
    {
        return view('livewire.transactions.create', [
            'categories' => Auth::user()?->walletCategories()->pluck('title', 'id'),
            'wallets' => Auth::user()?->wallets()->pluck('title', 'id'),
        ]);
    }
}
