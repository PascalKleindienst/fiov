<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Concerns\WithBreadcrumbs;
use App\Data\BreadcrumbItemData;
use App\Models\User;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * @property-read LengthAwarePaginator<int, User> $users
 */
#[Layout('components.layouts.admin')]
#[Title('users.index')]
final class Users extends Component
{
    use WithBreadcrumbs;
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
        $this->withBreadcrumbs(new BreadcrumbItemData(__('users.index')));
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::paginate(12);
    }

    public function delete(User $user): void
    {
        $this->authorize('delete', $user);

        $user->delete();

        Flux::modal('confirm-user-deletion-'.$user->id)->close();
        Flux::toast('User deleted successfully', 'Success', variant: 'success');

        $this->redirect(route('admin.users'), navigate: true);
    }
}
