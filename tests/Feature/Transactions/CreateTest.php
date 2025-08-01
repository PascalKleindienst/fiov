<?php

declare(strict_types=1);

use App\Enums\Icon;
use App\Livewire\Transactions\Create;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use App\Models\WalletTransaction;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->wallet = Wallet::factory()->for($this->user, 'user')->create();
    $this->category = WalletCategory::factory()->for($this->user, 'user')->create();
});

it('requires authentication to access the component', closure: function (): void {
    Livewire::test(Create::class)->assertForbidden();
    Livewire::actingAs($this->user)->test(Create::class)->assertOk();
});

it('renders the create view', function (): void {
    Livewire::actingAs($this->user)->test(Create::class)
        ->assertViewIs('livewire.transactions.create')
        ->assertViewHas('categories', $this->user->walletCategories()->pluck('title', 'id'))
        ->assertViewHas('wallets', $this->user->wallets()->pluck('title', 'id'))
        ->assertOk();
});

it('can create a new transaction with valid data', function (): void {
    Livewire::actingAs($this->user)->test(Create::class)
        ->set('form.title', 'New Transaction')
        ->set('form.wallet_category_id', $this->category->id)
        ->set('form.wallet_id', $this->wallet->id)
        ->set('form.amount', 1000)
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.currency', 'EUR')
        ->set('form.icon', Icon::PiggyBank->value)
        ->call('save')
        ->assertRedirect(route('dashboard'));

    $transaction = WalletTransaction::firstOrFail();

    expect($transaction->title)->toBe('New Transaction')
        ->and($transaction->amount->getAmount())->toEqual(1000 * 100)
        ->and($transaction->currency)->toBe('EUR')
        ->and($transaction->wallet_category_id)->toBe($this->category->id)
        ->and($transaction->wallet_id)->toBe($this->wallet->id)
        ->and($transaction->is_investment)->toBeFalse()
        ->and($transaction->icon)->toEqual(Icon::PiggyBank);
});

it('validates fields on create', function ($property, $value, $rule): void {
    Livewire::actingAs($this->user)->test(Create::class)
        ->set($property, $value)
        ->call('save')
        ->assertHasErrors([$property => $rule]);
})->with([
    ['form.title', '', 'required'],
    ['form.amount', '', 'required'],
    ['form.wallet_category_id', '', 'required'],
    ['form.wallet_id', '', 'required'],
    ['form.icon', 'invalid', \Illuminate\Validation\Rules\Enum::class],
    ['form.recurring_frequency', 'invalid', \Illuminate\Validation\Rules\Enum::class],
    ['form.recurring_end_date', 'invalid', 'date'],
    ['form.recurring_end_date', now()->subYear(), 'after:today'],
]);
