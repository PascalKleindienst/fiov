<?php

declare(strict_types=1);

use App\Livewire\Charts\Spendings;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use App\Models\WalletTransaction;
use Livewire\Livewire;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->seed(\Database\Seeders\DemoDataSeeder::class);
});

it('requires authentication to access the component', function (): void {
    get(route('dashboard'))->assertRedirectToRoute('login');

    actingAs(User::first());
    get(route('dashboard'))->assertOk();
});

it('shows the spendings chart for the authenticated user', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act & Assert
    Livewire::actingAs($user)->test(Spendings::class)
        ->assertViewIs('livewire.charts.chart')
        ->assertSeeText(__('charts.spendings'))
        ->assertOk();
});

it('displays spending transactions grouped by time interval and category', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();
    $category1 = WalletCategory::factory()->for($user, 'user')->create(['title' => 'Food']);
    $category2 = WalletCategory::factory()->for($user, 'user')->create(['title' => 'Transport']);

    // Create spending transactions
    WalletTransaction::factory()->for($wallet)->for($category1, 'category')->create([
        'amount' => -3000, // $30.00
    ]);

    WalletTransaction::factory()->for($wallet)->for($category2, 'category')->create([
        'amount' => -2000, // $20.00
    ]);

    // Act
    $component = Livewire::actingAs($user)->test(Spendings::class);

    // Assert
    $component->assertViewIs('livewire.charts.chart');
    $chart = $component->instance()->chart();
    $seriesNames = $chart->series->pluck('name')->toArray();

    expect($chart->name)->toBe(__('charts.spendings'))
        ->and($chart->currency)->toBe($wallet->currency->value)
        ->and($chart->total())->toBeGreaterThan(0)
        ->and($seriesNames)->toContain('Food')
        ->and($seriesNames)->toContain('Transport')
        ->and($chart->colors->count())->toBeGreaterThan(0);
});

it('can change the interval for the chart', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(Spendings::class);

    // Assert
    expect($component->instance()->interval)->toBe('month');

    $component->set('interval', 'year')
        ->assertSet('interval', 'year');

    expect($component->instance()->chart())->toBeInstanceOf(\App\Data\Chart::class);
});

it('has stacked chart options', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(Spendings::class);
    $chart = $component->instance()->chart();

    // Convert to array to access options
    $chartArray = $chart->toArray();

    // Assert stacked chart options
    expect($chartArray)->toHaveKey('chart')
        ->and($chartArray['chart'])->toHaveKey('stacked')
        ->and($chartArray['chart']['stacked'])->toBeTrue();
});
