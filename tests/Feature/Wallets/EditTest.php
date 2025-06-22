<?php

declare(strict_types=1);

use App\Enums\Color;
use App\Enums\Icon;
use App\Livewire\Wallets\Edit;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('requires authentication to access the component', function (): void {
    get(route('wallets.edit', Wallet::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can only edit a wallet if it belongs to the authenticated user', function (): void {
    $user = User::factory()->create();
    actingAs($user);

    get(route('wallets.edit', Wallet::factory()->create()))->assertForbidden();
    get(route('wallets.edit', Wallet::factory()->for($user, 'user')->create()))
        ->assertOk();
});

it('can mount with existing wallet for editing', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Edit::class, ['wallet' => $wallet])
        ->assertOk()
        ->assertSet('form.model.id', $wallet->id)
        ->assertSee($wallet->title);
});

it('can update an existing wallet', function (): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create([
        'title' => 'Old Title',
        'color' => Color::Red->value,
        'icon' => Icon::Star->value,
    ]);

    Livewire::actingAs($user)->test(Edit::class, ['wallet' => $wallet])
        ->set('form.title', 'Updated Title')
        ->set('form.color', Color::Green->value)
        ->set('form.icon', Icon::Wallet->value)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('wallets.index'));

    $wallet->refresh();
    expect($wallet->title)->toBe('Updated Title')
        ->and($wallet->color)->toEqual(Color::Green)
        ->and($wallet->icon)->toEqual(Icon::Wallet);
});

it('validates fields on update', function ($property, $value, $rule): void {
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(Edit::class, ['wallet' => $wallet])
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
