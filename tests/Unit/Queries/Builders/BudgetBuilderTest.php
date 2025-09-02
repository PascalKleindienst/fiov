<?php

declare(strict_types=1);

namespace Tests\Unit\Queries\Builders;

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Enums\Priority;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('filters by active status', function (): void {
    $activeBudget = Budget::factory()->for($this->user)->create(['status' => BudgetStatus::Active]);
    $inactiveBudget = Budget::factory()->for($this->user)->create(['status' => BudgetStatus::Completed]);

    $budgets = Budget::query()->active()->get();

    expect($budgets)->toHaveCount(1)
        ->and($budgets->first()->id)->toBe($activeBudget->id);
});

it('filters by default type', function (): void {
    $defaultBudget = Budget::factory()->for($this->user)->create(['type' => BudgetType::Default]);
    $goalBasedBudget = Budget::factory()->for($this->user)->create(['type' => BudgetType::Monthly]);

    $budgets = Budget::query()->default()->get();

    expect($budgets)->toHaveCount(1)
        ->and($budgets->first()->id)->toBe($defaultBudget->id);
});

it('filters by goal based type', function (): void {
    $defaultBudget = Budget::factory()->for($this->user)->create(['type' => BudgetType::Default]);
    $goalBasedBudget = Budget::factory()->for($this->user)->create(['type' => BudgetType::SavingsGoal]);

    $budgets = Budget::query()->goalBased()->get();

    expect($budgets)->toHaveCount(1)
        ->and($budgets->first()->id)->toBe($goalBasedBudget->id);
});

it('filters by priority', function (): void {
    $highPriorityBudget = Budget::factory()->for($this->user)->create(['priority' => Priority::High]);
    $lowPriorityBudget = Budget::factory()->for($this->user)->create(['priority' => Priority::Low]);

    $budgets = Budget::query()->priority(Priority::High)->get();

    expect($budgets)->toHaveCount(1)
        ->and($budgets->first()->id)->toBe($highPriorityBudget->id);
});
