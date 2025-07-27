<?php

declare(strict_types=1);

namespace App\Livewire\Wallets;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Models\Wallet;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithBreadcrumbs;
    use WithPagination;

    public function deleteWallet(Wallet $wallet): void
    {
        $this->authorize('delete', $wallet);
        $wallet->delete();
        Flux::toast(__('wallets.deleted', ['name' => $wallet->title]));
        Flux::modal('confirm-deletion-'.$wallet->id)->close();
    }

    public function render(): View
    {
        $this->withBreadcrumbs(new BreadcrumbItemData(__('wallets.index')));

        return view('livewire.wallets.index', [
            'wallets' => Auth::user()?->wallets()->paginate(12),
        ]);
    }
}
