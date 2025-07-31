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
            self::WEEK => now()->subWeeks(2)->startOfDay(),
            self::MONTH => now()->subMonths(2)->startOfDay(),
            self::YEAR => now()->subYears(2)->startOfDay(),
            default => now()->subDays(2)->startOfDay(),
        };

        $to = match ($this->interval) {
            self::WEEK => now()->subWeek()->endOfDay(),
            self::MONTH => now()->subMonth()->endOfDay(),
            self::YEAR => now()->subYear()->endOfDay(),
            default => now()->subDay()->endOfDay(),
        };

        return $query->where('created_at', '>=', $from->toDateString())
            ->where('created_at', '<', $to->toDateString())
            ->orderBy('created_at', $this->direction);
    }
}
