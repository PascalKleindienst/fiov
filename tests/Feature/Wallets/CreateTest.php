<?php

declare(strict_types=1);

use App\Enums\Color;
use App\Enums\Icon;
use App\Livewire\Wallets\Create;
use App\Models\User;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

it('requires authentication to access the component', function (): void {
    get(route('wallets.create'))->assertRedirectToRoute('login');

    $user = User::factory()->create();
    actingAs($user);
    get(route('wallets.create'))->assertOk();
});

it('renders the create view', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->assertViewIs('livewire.wallets.create-or-edit')
        ->assertOk();
});

it('can create a new wallet category with valid data', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set('form.title', 'Sparen')
        ->set('form.description', 'Some Description for you')
        ->set('form.color', Color::Red->value)
        ->set('form.icon', Icon::PiggyBank->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('wallets.index'));

    assertDatabaseHas('wallets', [
        'title' => 'Sparen',
        'color' => Color::Red->value,
        'icon' => Icon::PiggyBank->value,
        'user_id' => $user->id,
    ]);
});

it('validates fields on create', function ($property, $value, $rule): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set($property, $value)
        ->call('save')
        ->assertHasErrors([$property => $rule]);
})->with([
    ['form.title', '', 'required'],
    ['form.description', '', 'required'],
    ['form.color', 'invalid', \Illuminate\Validation\Rules\Enum::class],
    ['form.icon', 'invalid', \Illuminate\Validation\Rules\Enum::class],
    ['form.currency', 'invalid', 'currency'],
]);
