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

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->wallet = \App\Models\Wallet::factory()->for($this->user)->create();
});

it('renders successfully', function (): void {
    actingAs(User::factory()->create());
    get(route('budgets.index'))
        ->assertOk()
        ->assertSee(__('budgets.index'));
});

it('displays a list of budgets', function (): void {
    Budget::factory()->count(3)->for($this->user)->for($this->wallet)->create();

    Livewire::actingAs($this->user)->test(BudgetList::class)
        ->assertSet('budgets', static function ($budgets): true {
            expect($budgets)->toHaveCount(3);

            return true;
        });
});

it('does not show budgets of archived wallets', function (): void {
    Budget::factory()->count(3)->for($this->user)->for($this->wallet)->create();
    Budget::factory()->for($this->user)->for(\App\Models\Wallet::factory()->for($this->user)->archived()->create())->create();

    Livewire::actingAs($this->user)->test(BudgetList::class)
        ->assertSet('budgets', static function ($budgets): true {
            expect($budgets)->toHaveCount(3);

            return true;
        });
});

it('displays a message when no budgets are found', function (): void {
    Budget::query()->delete();

    Livewire::actingAs($this->user)->test(BudgetList::class)
        ->assertSee(__('budgets.empty.title'));
});

it('filters budgets by type', function (): void {
    $budgetToSee = Budget::factory()->for($this->user)->for($this->wallet)->create(['type' => BudgetType::Monthly]);
    Budget::factory()->for($this->user)->for($this->wallet)->create(['type' => BudgetType::Weekly]);

    Livewire::actingAs($this->user)->test(BudgetList::class)
        ->set('type', BudgetType::Monthly->value)
        ->assertSee($budgetToSee->name)
        ->assertSet('budgets', function ($budgets): true {
            expect($budgets)->toHaveCount(1);

            return true;
        });
});

it('filters budgets by status', function (): void {
    $budgetToSee = Budget::factory()->for($this->user)->for($this->wallet)->create(['status' => BudgetStatus::Active]);
    Budget::factory()->for($this->user)->for($this->wallet)->create(['status' => BudgetStatus::Completed]);

    Livewire::actingAs($this->user)->test(BudgetList::class)
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
