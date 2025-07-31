<?php

declare(strict_types=1);

namespace App\Queries;

use App\Contracts\FilterInterface;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;

/**
 * @implements FilterInterface<WalletTransaction>
 */
final readonly class TransactionsByInterval implements FilterInterface
{
    public const WEEK = 'week';

    public const MONTH = 'month';

    public const YEAR = 'year';

    public function __construct(
        private string $interval,
        private string $direction = 'asc',
    ) {}

    public function __invoke(Builder $query): Builder
    {
        $dayInterval = match ($this->interval) {
            self::WEEK => now()->subWeek()->startOfDay(),
            self::MONTH => now()->subMonth()->startOfDay(),
            self::YEAR => now()->subYear()->startOfDay(),
            default => now()->subDay()->startOfDay(),
        };

        return $query->where('created_at', '>=', $dayInterval->toDateString())
            ->orderBy('created_at', $this->direction);
    }
}
