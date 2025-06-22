<?php

declare(strict_types=1);

use App\Enums\Color;
use App\Enums\Icon;
use App\Livewire\Categories\Edit;
use App\Models\User;
use App\Models\WalletCategory;
use Livewire\Livewire;

it('requires authentication to access the component', function (): void {
    Livewire::test(Edit::class)
        ->assertForbidden();
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

it('validates required title', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Edit::class)
        ->set('form.title', '')
        ->call('save')
        ->assertHasErrors(['form.title' => 'required']);
});

it('validates color as a valid enum value', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Edit::class)
        ->set('form.title', 'Test')
        ->set('form.color', 'invalid-color')
        ->call('save')
        ->assertHasErrors(['form.color' => \Illuminate\Validation\Rules\Enum::class]);
});

it('validates icon as a valid enum value', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Edit::class)
        ->set('form.title', 'Test')
        ->set('form.icon', 'not-an-icon')
        ->call('save')
        ->assertHasErrors(['form.icon' => \Illuminate\Validation\Rules\Enum::class]);
});
