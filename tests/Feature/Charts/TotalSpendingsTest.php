<?php

declare(strict_types=1);

use App\Livewire\Charts\TotalSpendings;
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

it('shows the total spendings chart for the authenticated user', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    Livewire::actingAs($user)->test(TotalSpendings::class)
        ->assertViewIs('livewire.charts.chart')
        ->assertSeeText(__('charts.total_spendings'))
        ->assertOk();
});

it('displays spending transactions grouped by category', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();
    $category = WalletCategory::factory()->for($user, 'user')->create(['title' => 'Food']);

    // Create a spending transaction
    WalletTransaction::factory()->for($wallet)->for($category, 'category')->create([
        'amount' => -5000, // $50.00
    ]);

    // Act
    $component = Livewire::actingAs($user)->test(TotalSpendings::class);

    // Assert
    $component->assertViewIs('livewire.charts.chart');

    $chart = $component->instance()->chart();

    // Assert chart properties
    expect($chart->name)->toBe(__('charts.total_spendings'))
        ->and($chart->currency)->toBe($wallet->currency->value)
        ->and($chart->total())->toBeGreaterThan(0);

    // Assert the chart contains the category
    $seriesData = $chart->series->first()['data'] ?? [];
    $categoryFound = false;

    foreach ($seriesData as $dataPoint) {
        if ($dataPoint['x'] === 'Food') {
            $categoryFound = true;
            expect($dataPoint['y'])->toBe(50.0);
            break;
        }
    }

    expect($categoryFound)->toBeTrue();
});

it('can change the interval for the chart', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(TotalSpendings::class);

    // Default interval is MONTH
    expect($component->instance()->interval)->toBe('month');

    // Change interval to YEAR
    $component->set('interval', 'year')
        ->assertSet('interval', 'year');

    // Chart should be recomputed with new interval
    $chart = $component->instance()->chart();
    expect($chart)->toBeInstanceOf(\App\Data\Chart::class);
});
