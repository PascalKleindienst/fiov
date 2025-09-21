<?php

declare(strict_types=1);

namespace App\Livewire\Budgets;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

use function in_array;

/**
 * @property-read LengthAwarePaginator<int, Budget> $budgets
 */
#[Title('budgets.index')]
final class BudgetList extends Component
{
    use WithBreadcrumbs;
    use WithPagination;

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Budget::class);

        $this->withBreadcrumbs(new BreadcrumbItemData(__('budgets.index')));
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'status'])) {
            $this->resetPage();
        }
    }

    /**
     * @return LengthAwarePaginator<int, Budget>
     */
    #[Computed]
    public function budgets(): LengthAwarePaginator
    {
        return Budget::query()
            ->with(['wallet', 'categories'])
            ->whereHas('wallet')
            ->when($this->type, static fn (Builder $query, string $type): Builder => $query->where('type', $type))
            ->when($this->status, static fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->orderBy('end_date', 'asc')
            ->orderBy('start_date', 'desc')
            ->paginate(12);
    }
}
