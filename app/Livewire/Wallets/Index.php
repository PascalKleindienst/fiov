<?php

declare(strict_types=1);

namespace App\Livewire\Wallets;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Models\User;
use App\Models\Wallet;
use Flux\Flux;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('wallets.index')]
final class Index extends Component
{
    use WithBreadcrumbs;
    use WithPagination;

    public bool $showArchived = false;

    public function deleteWallet(Wallet $wallet): void
    {
        $this->authorize('force-delete', $wallet);
        $wallet->forceDelete();
        Flux::toast(__('wallets.deleted', ['name' => $wallet->title]));
        Flux::modal('confirm-deletion-'.$wallet->id)->close();
    }

    public function archiveWallet(Wallet $wallet): void
    {
        $this->authorize('delete', $wallet);
        $wallet->delete();
        Flux::toast(__('wallets.archived', ['name' => $wallet->title]), variant: 'success');
    }

    public function restoreWallet(int $walletId): void
    {
        $wallet = Wallet::withTrashed()->findOrFail($walletId);
        $this->authorize('restore', $wallet);
        $wallet->restore();
        Flux::toast(__('wallets.reactivated', ['name' => $wallet->title]), variant: 'success');
    }

    public function render(#[CurrentUser] User $user): View
    {
        $this->withBreadcrumbs(new BreadcrumbItemData(__('wallets.index')));

        return view('livewire.wallets.index', [
            'wallets' => $user->wallets()
                ->when($this->showArchived, static fn (Builder $query): Builder => $query->withTrashed())
                ->paginate(12),
        ]);
    }
}
