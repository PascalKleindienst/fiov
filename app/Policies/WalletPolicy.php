<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Access\HandlesAuthorization;

final readonly class WalletPolicy
{
    use HandlesAuthorization;

    public function viewAny(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, Wallet $wallet): bool
    {
        return $wallet->user_id === $user->id;
    }

    public function delete(User $user, Wallet $wallet): bool
    {
        return $wallet->user_id === $user->id;
    }

    public function restore(User $user, Wallet $wallet): bool
    {
        return $wallet->user_id === $user->id;
    }

    public function forceDelete(User $user, Wallet $wallet): bool
    {
        return $wallet->user_id === $user->id;
    }
}
