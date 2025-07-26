<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\Session;

final class WalletService
{
    public function current(): Wallet
    {
        if (! Session::has('wallet') || ! Session::get('wallet') instanceof Wallet) {
            Session::put('wallet', Wallet::query()->firstOrFail()->id);
        }

        return Wallet::findOrFail((int) Session::get('wallet'));
    }

    /**
     * TODO: Implement switching active wallets
     */
}
