<?php

declare(strict_types=1);

use App\Enums\Color;
use App\Enums\Icon;
use App\Livewire\Categories\Create;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication to access the component', function (): void {
    get(route('categories.create'))->assertRedirectToRoute('login');

    $user = User::factory()->create();
    actingAs($user);
    get(route('categories.create'))->assertOk();
});

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

    $category = $user->walletCategories()->first();

    expect($category->title)->toBe('Sparen')
        ->and($category->color)->toEqual(Color::Red)
        ->and($category->icon)->toEqual(Icon::PiggyBank);
});

it('validates fields on create', function ($property, $value, $rule): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(Create::class)
        ->set($property, $value)
        ->call('save')
        ->assertHasErrors([$property => $rule]);
})->with([
    ['form.title', '', 'required'],
    ['form.color', 'invalid', \Illuminate\Validation\Rules\Enum::class],
    ['form.icon', 'invalid', \Illuminate\Validation\Rules\Enum::class],
]);
