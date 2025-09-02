<?php

declare(strict_types=1);

use App\Enums\BudgetStatus;
use App\Livewire\Budgets\BudgetCard;
use App\Models\Budget;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the budget card correctly', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    Livewire::actingAs($user)->test(BudgetCard::class, ['budget' => $budget])
        ->assertSee($budget->title)
        ->assertSee($budget->type->label())
        ->assertSee($budget->status->label());
});

it('can pause and resume a budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create(['status' => BudgetStatus::Active]);

    actingAs($user);
    $component = Livewire::test(BudgetCard::class, ['budget' => $budget]);

    $component->call('pause');

    $budget->refresh();
    expect($budget->status)->toBe(BudgetStatus::Paused);

    $component->call('resume');
    $budget->refresh();
    expect($budget->status)->toBe(BudgetStatus::Active);
});

it('can delete a budget', function (): void {
    $user = User::factory()->create();
    $budget = Budget::factory()->for($user)->create();

    Livewire::actingAs($user)->test(BudgetCard::class, ['budget' => $budget])
        ->call('delete');

    $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
});
