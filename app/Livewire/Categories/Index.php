<?php

declare(strict_types=1);

namespace App\Livewire\Categories;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Models\WalletCategory;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Index extends Component
{
    use WithBreadcrumbs;
    use WithPagination;

    public function deleteCategory(WalletCategory $category): void
    {
        $this->authorize('delete', $category);
        $category->delete();
        Flux::toast(__('categories.deleted', ['name' => $category->title]));
        Flux::modal('confirm-category-deletion-'.$category->id)->close();
    }

    /**
     * @return LengthAwarePaginator<int, WalletCategory>
     */
    #[Computed]
    public function categories(): LengthAwarePaginator
    {
        abort_if(! Auth::user(), 403);

        return Auth::user()->walletCategories()->orderBy('title')->paginate(12);
    }

    public function render(): View
    {
        $this->withBreadcrumbs(new BreadcrumbItemData(__('categories.index')));

        return view('livewire.categories.index');
    }
}
