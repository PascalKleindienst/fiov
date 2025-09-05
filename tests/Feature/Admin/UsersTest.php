<?php

declare(strict_types=1);

use App\Livewire\Admin\Users;
use App\Models\License;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->admin = User::factory()->admin()->create();
    actingAs($this->admin);

    // Create pro license
    withProLicense();
});

it('renders successfully', function (): void {
    get(route('admin.users'))
        ->assertStatus(200);
});

it('redirects if the user is not an admin', function (): void {
    actingAs(User::factory()->create())->get(route('admin.users'))
        ->assertForbidden();
});

it('shows an error if we only have a community license', function (): void {
    License::query()->delete();
    get(route('admin.users'))
        ->assertForbidden();
});

it('shows a list of users', function (): void {
    $users = User::factory()->count(5)->create();

    Livewire::test(Users::class)
        ->assertSee($users->first()->name);
});

it('can delete a user', function (): void {
    $userToDelete = User::factory()->create();

    Livewire::test(Users::class)
        ->call('delete', $userToDelete->id);

    expect(User::find($userToDelete->id))->toBeNull();
});
