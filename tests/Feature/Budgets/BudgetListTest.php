<?php

declare(strict_types=1);

use App\Enums\BudgetStatus;
use App\Enums\BudgetType;
use App\Livewire\Budgets\BudgetList;
use App\Models\Budget;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders successfully', function (): void {
    actingAs(User::factory()->create());
    get(route('budgets.index'))
        ->assertOk()
        ->assertSee(__('budgets.index'));
});

it('displays a list of budgets', function (): void {
    $user = User::factory()->create();
    Budget::factory()->count(3)->for($user)->create();

    Livewire::actingAs($user)->test(BudgetList::class)
        ->assertSet('budgets', static function ($budgets): true {
            expect($budgets)->toHaveCount(3);

            return true;
        });
});

it('displays a message when no budgets are found', function (): void {
    $user = User::factory()->create();
    Budget::query()->delete();

    Livewire::actingAs($user)->test(BudgetList::class)
        ->assertSee(__('budgets.empty.title'));
});

it('filters budgets by type', function (): void {
    $user = User::factory()->create();
    $budgetToSee = Budget::factory()->for($user)->create(['type' => BudgetType::Monthly]);
    Budget::factory()->for($user)->create(['type' => BudgetType::Weekly]);

    Livewire::actingAs($user)->test(BudgetList::class)
        ->set('type', BudgetType::Monthly->value)
        ->assertSee($budgetToSee->name)
        ->assertSet('budgets', function ($budgets): true {
            expect($budgets)->toHaveCount(1);

            return true;
        });
});

it('filters budgets by status', function (): void {
    $user = User::factory()->create();
    $budgetToSee = Budget::factory()->for($user)->create(['status' => BudgetStatus::Active]);
    Budget::factory()->for($user)->create(['status' => BudgetStatus::Completed]);

    Livewire::actingAs($user)->test(BudgetList::class)
        ->set('status', BudgetStatus::Active->value)
        ->assertSee($budgetToSee->name)
        ->assertSet('budgets', function ($budgets): true {
            expect($budgets)->toHaveCount(1);

            return true;
        });
});

it('unauthenticated users cannot see the page', function (): void {
    get(route('budgets.index'))
        ->assertRedirect(route('login'));
});
