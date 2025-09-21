<?php

declare(strict_types=1);

use App\Livewire\Wallets\Index;
use App\Models\User;
use App\Models\Wallet;

use function Pest\Laravel\actingAs;
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

    \Pest\Laravel\assertModelMissing($wallet);
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

it('can archive a wallet if authorized', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->call('archiveWallet', $wallet)
        ->assertDispatched('toast-show');

    expect($wallet->fresh()->trashed())->toBeTrue();
});

it('can reactivate a wallet if authorized', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create(['deleted_at' => now()]);

    Livewire::actingAs($user)->test(Index::class)
        ->call('restoreWallet', $wallet->id)
        ->assertDispatched('toast-show');

    expect($wallet->fresh()->trashed())->toBeFalse();
});

it('does not show archived wallets by default', function (): void {
    $user = User::factory()->create();
    Wallet::factory()->for($user, 'user')->create();
    Wallet::factory()->for($user, 'user')->create(['deleted_at' => now()]);

    Livewire::actingAs($user)->test(Index::class)
        ->assertViewHas('wallets', fn ($wallets): bool => $wallets->count() === 1);
});

it('shows archived wallets when showArchived is true', function (): void {
    $user = User::factory()->create();
    $activeWallet = Wallet::factory()->for($user, 'user')->create();
    $archivedWallet = Wallet::factory()->for($user, 'user')->create(['deleted_at' => now()]);

    Livewire::actingAs($user)->test(Index::class)
        ->set('showArchived', true)
        ->assertSee($activeWallet->title)
        ->assertSee($archivedWallet->title);
});
