<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\RecurringTransaction;
use App\Models\User;

final readonly class RecurringTransactionPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, RecurringTransaction $recurringTransaction): bool
    {
        return $user->id === $recurringTransaction->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, RecurringTransaction $recurringTransaction): bool
    {
        return $user->id === $recurringTransaction->user_id;
    }

    public function delete(User $user, RecurringTransaction $recurringTransaction): bool
    {
        return $user->id === $recurringTransaction->user_id;

    }

    public function restore(User $user, RecurringTransaction $recurringTransaction): bool
    {
        return $user->id === $recurringTransaction->user_id;
    }

    public function forceDelete(User $user, RecurringTransaction $recurringTransaction): bool
    {
        return $user->id === $recurringTransaction->user_id;
    }
}
