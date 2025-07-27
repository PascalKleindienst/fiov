<?php

declare(strict_types=1);

use App\Enums\Color;
use App\Enums\Icon;
use App\Livewire\Categories\Edit;
use App\Models\User;
use App\Models\WalletCategory;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication to access the component', function (): void {
    get(route('categories.edit', WalletCategory::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can only edit a category if it belongs to the authenticated user', function (): void {
    $otherUser = User::factory()->create();
    actingAs($otherUser);
    $notOwnedCategory = WalletCategory::factory()->for($otherUser, 'user')->create();

    $user = User::factory()->create();
    actingAs($user);
    $ownCategory = WalletCategory::factory()->for($user, 'user')->create();

    get(route('categories.edit', $notOwnedCategory))->assertNotFound();
    get(route('categories.edit', $ownCategory))->assertOk();
});

it('can mount with existing category for editing', function (): void {
    $user = User::factory()->create();
    $category = WalletCategory::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Edit::class, ['walletCategory' => $category])
        ->assertOk()
        ->assertSet('form.model.id', $category->id)
        ->assertSee($category->title);
});

it('can update an existing category', function (): void {
    $user = User::factory()->create();
    $category = WalletCategory::factory()->for($user, 'user')->create([
        'title' => 'Old Title',
        'color' => Color::Red->value,
        'icon' => Icon::Star->value,
    ]);

    Livewire::actingAs($user)->test(Edit::class, ['walletCategory' => $category])
        ->set('form.title', 'Updated Title')
        ->set('form.color', Color::Green->value)
        ->set('form.icon', Icon::Wallet->value)
        ->call('save')
        ->assertRedirect(route('categories.index'));

    $category->refresh();
    expect($category->title)->toBe('Updated Title')
        ->and($category->color)->toEqual(Color::Green)
        ->and($category->icon)->toEqual(Icon::Wallet);
});

it('validates fields on update', function ($property, $value, $rule): void {
    $user = User::factory()->create();
    $category = WalletCategory::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Edit::class, ['walletCategory' => $category])
        ->set($property, $value)
        ->call('save')
        ->assertHasErrors([$property => $rule]);
})->with([
    ['form.title', '', 'required'],
    ['form.color', 'invalid', \Illuminate\Validation\Rules\Enum::class],
    ['form.icon', 'invalid', \Illuminate\Validation\Rules\Enum::class],
]);
