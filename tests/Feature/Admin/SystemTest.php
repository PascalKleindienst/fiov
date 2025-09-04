<?php

declare(strict_types=1);

use App\Livewire\Admin\System;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->admin()->create());
});

it('renders successfully', function (): void {
    Livewire::test(System::class)
        ->assertOk();
});

it('shows system information', function (): void {
    Livewire::test(System::class)
        ->assertViewHas('valid', true)
        ->assertViewHas('errors', false)
        ->assertOk();
});

it('requires authentication', function (): void {
    auth()->logout();
    \Pest\Laravel\get(route('admin.system'))
        ->assertRedirect(route('login'));
});
