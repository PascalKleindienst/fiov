<?php

declare(strict_types=1);

namespace App\Policies;

use App\Facades\LicenseService;
use App\Models\User;

final readonly class UserPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->level->isAdmin() && LicenseService::isPro()) {
            return true;
        }

        return null;
    }

    public function viewAny(): bool
    {
        return false;
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function create(): bool
    {
        return false;
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
