<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\WalletTransaction;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class TransactionCreatedEvent
{
    use Dispatchable;

    public function __construct(public WalletTransaction $transaction) {}
}
