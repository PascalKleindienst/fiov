<?php

declare(strict_types=1);

use App\Enums\BudgetType;
use App\Livewire\Budgets\CreateOrEditBudget;
use App\Models\Budget;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('renders the create budget page successfully', function (): void {
    $user = User::factory()->create();
    actingAs($user);

    get(route('budgets.create'))
        ->assertOk()
        ->assertSee(__('budgets.create'));
});

it('renders the edit budget page successfully', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $budget = Budget::factory()->for($user)->for($wallet)->create();
    actingAs($user);

    get(route('budgets.edit', $budget))
        ->assertOk()
        ->assertSee(__('budgets.edit', ['name' => $budget->title]));
});

it('can create a new budget', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $category = WalletCategory::factory()->for($user)->create();
    actingAs($user);

    Livewire::test(CreateOrEditBudget::class, ['budget' => new Budget()])
        ->set('form.title', 'Test Budget')
        ->set('form.amount', 1000)
        ->set('form.wallet_id', $wallet->id)
        ->set('form.type', BudgetType::Monthly->value)
        ->set('form.start_date', now()->toDateString())
        ->set('form.selectedCategories', [$category->id])
        ->set('form.allocatedAmounts', [$category->id => 1000])
        ->call('save');

    $this->assertDatabaseHas('budgets', ['title' => 'Test Budget']);
});

it('can update an existing budget', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user)->create();
    $budget = Budget::factory()->for($user)->for($wallet)->create();
    $category = WalletCategory::factory()->for($user)->create();
    actingAs($user);

    Livewire::test(CreateOrEditBudget::class, ['budget' => $budget])
        ->set('form.title', 'Updated Budget')
        ->set('form.amount', 2000)
        ->set('form.wallet_id', $wallet->id)
        ->set('form.type', BudgetType::Weekly->value)
        ->set('form.start_date', now()->toDateString())
        ->set('form.selectedCategories', [$category->id])
        ->set('form.allocatedAmounts', [$category->id => 2000])
        ->call('save')
        ->assertHasNoErrors();

    $budget = $budget->refresh();
    expect($budget)
        ->and($budget->title)->toBe('Updated Budget')
        ->and($budget->amount->getAmount())->toEqual(200000);
});

it('shows validation errors for invalid data', function (): void {
    $user = User::factory()->create();
    actingAs($user);

    Livewire::test(CreateOrEditBudget::class)
        ->set('form.title', '')
        ->set('form.amount', 0)
        ->call('save')
        ->assertHasErrors(['form.title', 'form.amount']);
});
