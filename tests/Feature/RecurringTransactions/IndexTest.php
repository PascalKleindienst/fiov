<?php

declare(strict_types=1);

use App\Livewire\RecurringTransactions\Index;
use App\Models\RecurringTransaction;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->seed(\Database\Seeders\DemoDataSeeder::class);
});

it('requires authentication to access the component', function (): void {
    get(route('recurring-transactions.index'))->assertRedirectToRoute('login');
    actingAs(User::first());
    get(route('recurring-transactions.index'))->assertOk();
});

it('shows a paginated list of recurring transactions for the authenticated user', function (): void {
    Livewire::actingAs(User::first())->test(Index::class)
        ->assertViewIs('livewire.recurring-transactions.index')
        ->assertSee(RecurringTransaction::orderBy('title')->first()?->title)
        ->assertOk();
});

it('can delete a recurring transaction if authorized', function (): void {
    $transaction = RecurringTransaction::first();

    Livewire::actingAs(User::first())->test(Index::class)
        ->call('delete', $transaction)
        ->assertDispatched('toast-show')
        ->assertDispatched('modal-close', name: 'confirm-deletion-'.$transaction->id);

    assertDatabaseMissing('recurring_transactions', [
        'id' => $transaction->id,
    ]);
});

it('cannot delete a recurring transaction if not authorized', function (): void {
    $transaction = RecurringTransaction::factory()->for(User::factory(), 'user')->create();

    Livewire::actingAs(User::factory()->create())->test(Index::class)
        ->call('delete', $transaction)
        ->assertForbidden();

    expect(RecurringTransaction::withoutGlobalScope(\App\Models\Scopes\OwnerScope::class)->find($transaction->id))->not()->toBeNull();
});
