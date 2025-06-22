<?php

declare(strict_types=1);

use App\Livewire\Wallets\Index;
use App\Models\User;
use App\Models\Wallet;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;

it('requires authentication to access the component', function (): void {
    get(route('wallets.index'))->assertRedirectToRoute('login');

    $user = User::factory()->create();
    actingAs($user);
    get(route('wallets.index'))->assertOk();
});

it('shows a paginated list of wallets for the authenticated user', function (): void {
    $user = User::factory()->create();
    Wallet::factory()->count(15)->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->assertViewIs('livewire.wallets.index')
        ->assertSee(Wallet::first()->title)
        ->assertOk();
});

it('can delete a wallet if authorized', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->call('deleteWallet', $wallet)
        ->assertDispatched('toast-show')
        ->assertDispatched('modal-close', name: 'confirm-deletion-'.$wallet->id);

    assertSoftDeleted($wallet);
});

it('cannot delete a category if not authorized', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $wallet = Wallet::factory()->for($otherUser, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->call('deleteWallet', $wallet)
        ->assertForbidden();

    \Pest\Laravel\assertNotSoftDeleted($wallet);
});
