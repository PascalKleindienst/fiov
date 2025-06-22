<?php

declare(strict_types=1);

use App\Livewire\Categories\Index;
use App\Models\User;
use App\Models\WalletCategory;

use function Pest\Laravel\assertDatabaseMissing;

it('requires authentication to access the component', function (): void {
    Livewire::test(Index::class)
        ->assertForbidden();
});

it('shows a paginated list of categories for the authenticated user', function (): void {
    $user = User::factory()->create();
    WalletCategory::factory()->count(15)->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->assertViewIs('livewire.categories.index')
        ->assertSee(WalletCategory::first()->title)
        ->assertOk();
});

it('can delete a category if authorized', function (): void {
    $user = User::factory()->create();
    $category = WalletCategory::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->call('deleteCategory', $category)
        ->assertDispatched('toast-show')
        ->assertDispatched('modal-close', name: 'confirm-category-deletion-'.$category->id);

    assertDatabaseMissing('wallet_categories', [
        'id' => $category->id,
    ]);
});

it('cannot delete a category if not authorized', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = WalletCategory::factory()->for($otherUser, 'user')->create();

    Livewire::actingAs($user)->test(Index::class)
        ->call('deleteCategory', $category)
        ->assertForbidden();

    expect(WalletCategory::find($category->id))->not()->toBeNull();
});
