<?php

declare(strict_types=1);

use App\Queries\TransactionsByInterval;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

beforeEach(function (): void {
    // Freeze time for consistent test results
    Carbon::setTestNow(now());
});

it('applies the interval when invalid interval is provided', function ($interval, $direction, $from): void {
    // Assert
    $builder = mock(Builder::class);
    $builder->shouldReceive('where')
        ->with('created_at', '>=', $from->toDateString())
        ->once()
        ->andReturnSelf();

    $builder->shouldReceive('orderBy')
        ->with('created_at', $direction)
        ->once()
        ->andReturnSelf();

    // Act
    $filter = new TransactionsByInterval($interval, $direction);
    $filter($builder);
})->with([
    'invalid_interval' => ['invalid_interval', 'asc', now()->subDay()->startOfDay()],
    'week' => [TransactionsByInterval::WEEK, 'desc', now()->subWeek()->startOfDay()],
    'month' => [TransactionsByInterval::MONTH, 'desc', now()->subMonth()->startOfDay()],
    'year' => [TransactionsByInterval::YEAR, 'desc', now()->subYear()->startOfDay()],
]);
