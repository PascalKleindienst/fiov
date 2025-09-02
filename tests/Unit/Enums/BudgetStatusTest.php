<?php

declare(strict_types=1);

use App\Enums\BudgetStatus;

it('returns the correct color', function (BudgetStatus $status, string $color): void {
    expect($status->color())->toBe($color);
})->with([
    [BudgetStatus::Active, 'sky'],
    [BudgetStatus::Completed, 'green'],
    [BudgetStatus::Cancelled, 'red'],
    [BudgetStatus::Paused, 'yellow'],
]);

it('has helpers for the states', function (BudgetStatus $status, string $helper, bool $expected): void {
    expect($status->$helper())->toBe($expected);
})->with([
    'isActive (Active)' => [BudgetStatus::Active, 'isActive', true],
    'isActive (Completed)' => [BudgetStatus::Completed, 'isActive', false],
    'isCompleted (Completed)' => [BudgetStatus::Completed, 'isCompleted', true],
    'isCompleted (Active)' => [BudgetStatus::Active, 'isCompleted', false],
    'isCancelled (Cancelled)' => [BudgetStatus::Cancelled, 'isCancelled', true],
    'isCancelled (Completed)' => [BudgetStatus::Completed, 'isCancelled', false],
    'isPaused (Paused)' => [BudgetStatus::Paused, 'isPaused', true],
    'isPaused (Completed)' => [BudgetStatus::Completed, 'isPaused', false],
]);

it('returns the values', function (): void {
    expect(BudgetStatus::values())
        ->toBeArray()
        ->toContain(
            'active', 'completed', 'cancelled', 'paused'
        );
});
