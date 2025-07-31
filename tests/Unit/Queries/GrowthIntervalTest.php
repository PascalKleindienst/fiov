<?php

declare(strict_types=1);

use App\Queries\GrowthInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    // Freeze time for consistent test results
    Carbon::setTestNow(now());
});

it('applies the interval when invalid interval is provided', function ($interval, $direction, $from, $to): void {
    // Assert
    $builder = mock(Builder::class);
    $builder->shouldReceive('where')
        ->with('created_at', '>=', $from->toDateString())
        ->once()
        ->andReturnSelf();

    $builder->shouldReceive('where')
        ->with('created_at', '<', $to->toDateString())
        ->once()
        ->andReturnSelf();

    $builder->shouldReceive('orderBy')
        ->with('created_at', $direction)
        ->once()
        ->andReturnSelf();

    // Act
    $filter = new GrowthInterval($interval, $direction);
    $filter($builder);
})->with([
    'invalid_interval' => ['invalid_interval', 'asc', now()->subDays(2)->startOfDay(), now()->subDay()->endOfDay()],
    'week' => [GrowthInterval::WEEK, 'desc', now()->subWeeks(2)->startOfDay(), now()->subWeek()->endOfDay()],
    'month' => [GrowthInterval::MONTH, 'desc', now()->subMonths(2)->startOfDay(), now()->subMonth()->endOfDay()],
    'year' => [GrowthInterval::YEAR, 'desc', now()->subYears(2)->startOfDay(), now()->subYear()->endOfDay()],
]);
