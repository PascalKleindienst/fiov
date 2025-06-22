<?php

declare(strict_types=1);

use App\Enums\Color;
use App\Enums\Icon;
use App\Livewire\Categories\Create;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('renders the create view', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->assertViewIs('livewire.categories.create-or-edit')
        ->assertOk();
});

it('can create a new wallet category with valid data', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set('form.title', 'Sparen')
        ->set('form.color', Color::Red->value)
        ->set('form.icon', Icon::PiggyBank->value)
        ->call('save')
        ->assertRedirect(route('categories.index'));

    assertDatabaseHas('wallet_categories', [
        'title' => 'Sparen',
        'color' => Color::Red->value,
        'icon' => Icon::PiggyBank->value,
        'user_id' => $user->id,
    ]);
});

it('validates required title on create', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set('form.title', '')
        ->call('save')
        ->assertHasErrors(['form.title' => 'required']);
});

it('validates color as a valid enum', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set('form.title', 'Test')
        ->set('form.color', 'invalid')
        ->call('save')
        ->assertHasErrors(['form.color' => \Illuminate\Validation\Rules\Enum::class]);
});

it('validates icon as a valid enum', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set('form.title', 'Test')
        ->set('form.icon', 'wrong-icon')
        ->call('save')
        ->assertHasErrors(['form.icon' => \Illuminate\Validation\Rules\Enum::class]);
});
