<?php

declare(strict_types=1);

use App\Livewire\Auth\Register;
use Livewire\Livewire;

use function Pest\Laravel\get;

test('registration is not rendererd by default', function (): void {
    get('/register')
        ->assertForbidden();
});

test('registration is only rendered for a signed request', function (): void {
    $route = \Illuminate\Support\Facades\URL::signedRoute('register', ['email' => 'test@example.com']);
    get($route)
        ->assertSee('test@example.com')
        ->assertOk();
});

test('new users can register', function (): void {
    $response = Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
