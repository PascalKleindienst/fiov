<?php

declare(strict_types=1);

namespace App\Livewire\Categories;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Livewire\Concerns\WithRules;
use App\Livewire\Forms\WalletCategoryForm;
use App\Models\WalletCategory;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class Edit extends Component
{
    use WithBreadcrumbs;
    use WithRules;

    public WalletCategoryForm $form;

    public function mount(?WalletCategory $walletCategory = null): void
    {
        abort_if(! Auth::user(), 403);

        $this->form->setModel($walletCategory?->loadMissing('rules'));

        $this->withBreadcrumbs(
            new BreadcrumbItemData(__('categories.index'), route('categories.index')),
            new BreadcrumbItemData($this->form->model->title ?? __('categories.create'))
        );
    }

    public function save(): void
    {
        $this->form->submit();

        Flux::toast(__('general.changes_have_been_saved'), variant: 'success');

        $this->redirect(route('categories.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.categories.create-or-edit')
            ->title(__('categories.edit', ['name' => $this->form->model?->title]));
    }
}
