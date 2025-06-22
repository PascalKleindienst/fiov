<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WalletCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @TODO
 */
final readonly class WalletCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, WalletCategory $walletCategory): bool
    {
        return $walletCategory->user_id === $user->id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, WalletCategory $walletCategory): bool
    {
        return $walletCategory->user_id === $user->id;
    }

    public function delete(User $user, WalletCategory $walletCategory): bool
    {
        return $walletCategory->user_id === $user->id;
    }

    public function restore(User $user, WalletCategory $walletCategory): bool
    {
        return $walletCategory->user_id === $user->id;
    }

    public function forceDelete(User $user, WalletCategory $walletCategory): bool
    {
        return $walletCategory->user_id === $user->id;
    }
}
