<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WalletTransaction;

final readonly class WalletTransactionPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user, WalletTransaction $transaction): bool
    {
        $transaction->loadMissing('wallet');

        return $user->id === $transaction->wallet->user_id;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user, WalletTransaction $transaction): bool
    {
        $transaction->loadMissing('wallet');

        return $user->id === $transaction->wallet->user_id;
    }

    public function delete(User $user, WalletTransaction $transaction): bool
    {
        $transaction->loadMissing('wallet');

        return $user->id === $transaction->wallet->user_id;
    }

    public function restore(User $user, WalletTransaction $transaction): bool
    {
        $transaction->loadMissing('wallet');

        return $user->id === $transaction->wallet->user_id;
    }

    public function forceDelete(User $user, WalletTransaction $transaction): bool
    {
        $transaction->loadMissing('wallet');

        return $user->id === $transaction->wallet->user_id;
    }
}
