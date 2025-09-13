<?php

declare(strict_types=1);

namespace App\Livewire\Wallets;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Livewire\Forms\WalletForm;
use App\Models\Wallet;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Edit extends Component
{
    use WithBreadcrumbs;

    public WalletForm $form;

    public function mount(?Wallet $wallet = null): void
    {
        $this->form->setModel($wallet);

        $this->withBreadcrumbs(
            new BreadcrumbItemData(__('wallets.index'), route('wallets.index')),
            new BreadcrumbItemData($this->form->model->title ?? __('wallets.create'))
        );
    }

    public function save(): void
    {
        $this->form->submit();

        Flux::toast(__('general.changes_have_been_saved'), variant: 'success');

        $this->redirect(route('wallets.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.wallets.create-or-edit')
            ->title(__('wallets.edit', ['name' => $this->form->model?->title]));
    }
}
