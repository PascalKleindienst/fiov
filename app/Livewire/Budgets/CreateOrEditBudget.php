<?php

declare(strict_types=1);

namespace App\Livewire\Budgets;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Livewire\Forms\BudgetForm;
use App\Models\Budget;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * @property-read Collection<int, WalletCategory> $filteredCategories
 * @property-read float $remainingAmount
 * @property-read float $totalAllocatedAmount
 */
final class CreateOrEditBudget extends Component
{
    use WithBreadcrumbs;

    public BudgetForm $form;

    /** @var Collection<int, Wallet> */
    public Collection $wallets;

    public string $search = '';

    /** @var Collection<int, WalletCategory> */
    private Collection $categories;

    public function updated(string $property): void
    {
        if ($property === 'form.wallet_id') {
            $this->form->resetWallet($this->wallets->first(fn (Wallet $wallet): bool => $wallet->id === $this->form->wallet_id));
        }
    }

    public function boot(): void
    {
        abort_if(! auth()->user(), 403);

        $this->categories = auth()->user()->walletCategories()->get();
        $this->wallets = auth()->user()->wallets()->get();
    }

    public function mount(Budget $budget): void
    {
        $this->authorize('create', Budget::class);

        if ($budget->exists) {
            $this->authorize('update', $budget);

            $this->form->isEdit = true;
            $this->form->model = $budget;
            $this->form->currency = $budget->wallet->currency->value;

            $this->form->fill([
                'title' => $budget->title,
                'description' => $budget->description,
                'type' => $budget->type,
                'status' => $budget->status,
                'amount' => (float) $budget->amount->getAmount() / 100,
                'start_date' => $budget->start_date->toDateString(),
                'end_date' => $budget->end_date?->toDateString(),
                'priority' => $budget->priority,
                'wallet_id' => $budget->wallet_id,
                'selectedCategories' => $budget->categories->pluck('id')->toArray(),
                'allocatedAmounts' => $budget->categories->mapWithKeys(static fn (WalletCategory $category) => [
                    $category->id => (float) $category->pivot?->allocated_amount->getAmount() / 100,
                ])->toArray(),
            ]);
        }

        $this->withBreadcrumbs(
            new BreadcrumbItemData(__('budgets.index'), route('budgets.index')),
            new BreadcrumbItemData($this->form->title ?? __('budgets.create')),
        );
    }

    public function save(): void
    {
        $this->form->submit();

        Flux::toast(__('general.changes_have_been_saved'), variant: 'success');

        $this->redirect(route('budgets.index'));
    }

    /**
     * @return Collection<int, WalletCategory>
     */
    #[Computed]
    public function filteredCategories(): Collection
    {
        if ($this->search === '' || $this->search === '0') {
            return $this->categories;
        }

        return $this->categories->filter(fn (WalletCategory $category) => Str::contains($category->title, $this->search, true));
    }

    #[Computed]
    public function remainingAmount(): float
    {
        return max(0, $this->form->amount - $this->totalAllocatedAmount);
    }

    #[Computed]
    public function totalAllocatedAmount(): float
    {
        return array_sum($this->form->allocatedAmounts);
    }
}
