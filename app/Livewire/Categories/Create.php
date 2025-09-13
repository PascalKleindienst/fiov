<?php

declare(strict_types=1);

namespace App\Livewire\Categories;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Livewire\Concerns\WithRules;
use App\Livewire\Forms\WalletCategoryForm;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('categories.create')]
final class Create extends Component
{
    use WithBreadcrumbs;
    use WithRules;

    public WalletCategoryForm $form;

    public function save(): void
    {
        $this->form->submit();
        $this->redirect(route('categories.index'));
    }

    public function render(): View
    {
        $this->withBreadcrumbs(
            new BreadcrumbItemData(__('categories.index'), route('categories.index')),
            new BreadcrumbItemData($this->form->model->title ?? __('categories.create'))
        );

        return view('livewire.categories.create-or-edit');
    }
}
