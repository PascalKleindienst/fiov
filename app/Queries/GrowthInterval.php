<?php

declare(strict_types=1);

namespace App\Queries;

use App\Contracts\FilterInterface;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;

/**
 * @implements FilterInterface<WalletTransaction>
 */
final readonly class GrowthInterval implements FilterInterface
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
        $from = match ($this->interval) {
            self::WEEK => now()->subDays(7 * 2)->startOfDay(),
            self::MONTH => now()->subDays(30 * 2)->startOfDay(),
            self::YEAR => now()->subDays(365 * 2)->startOfDay(),
            default => now()->subDays(2)->startOfDay(),
        };

        $to = match ($this->interval) {
            self::WEEK => now()->subDays(7)->endOfDay(),
            self::MONTH => now()->subDays(30)->endOfDay(),
            self::YEAR => now()->subDays(365)->endOfDay(),
            default => now()->subDays(1)->endOfDay(),
        };

        return $query->where('created_at', '>=', $from)
            ->where('created_at', '<', $to)
            ->orderBy('created_at', $this->direction);
    }
}
