<?php

declare(strict_types=1);

use App\Livewire\Charts\Investments;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

// Set up the test environment
beforeEach(function () {
    // Ensure the dashboard route exists
    expect(Route::has('dashboard'))->toBeTrue();
    $this->seed(DemoDataSeeder::class);
});

it('requires authentication to access the component', function (): void {
    get(route('dashboard'))->assertRedirectToRoute('login');

    actingAs(User::first());
    get(route('dashboard'))->assertOk();
});

it('shows the investments chart for the authenticated user', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act & Assert
    Livewire::actingAs($user)->test(Investments::class)
        ->assertViewIs('livewire.charts.chart')
        ->assertOk();
});

it('displays investment transactions accumulated by time interval', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Create investment transactions
    WalletTransaction::factory()->for($wallet)->create([
        'amount' => -10000, // $100.00 investment
        'is_investment' => true,
    ]);

    WalletTransaction::factory()->for($wallet)->create([
        'amount' => -5000, // $50.00 investment
        'is_investment' => true,
    ]);

    // Create a non-investment transaction that should be ignored
    WalletTransaction::factory()->for($wallet)->create([
        'amount' => -3000, // $30.00 expense
        'is_investment' => false,
    ]);

    // Act & Assert
    $component = Livewire::actingAs($user)->test(Investments::class);
    $component->assertViewIs('livewire.charts.chart');

    $chart = $component->instance()->chart();

    // Assert chart properties
    expect($chart->name)->toBe(__('charts.investments'))
        ->and($chart->currency)->toBe($wallet->currency->value)
        ->and($chart->total())->toBe(150.0)
        ->and($chart->series->first()['data'] ?? [])->not->toBeEmpty();
});

it('can change the interval for the chart', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(Investments::class);

    // Default interval is YEAR
    expect($component->instance()->interval)->toBe('year');

    // Change interval to MONTH
    $component->set('interval', 'month')
        ->assertSet('interval', 'month');

    // Chart should be recomputed with new interval
    $chart = $component->instance()->chart();
    expect($chart)->toBeInstanceOf(\App\Data\Chart::class);
});

it('has area chart options with green color', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(Investments::class);
    $chart = $component->instance()->chart();

    // Convert to array to access options
    $chartArray = $chart->toArray();

    // Assert area chart options
    expect($chartArray)->toHaveKey('chart')
        ->and($chartArray['chart'])->toHaveKey('type')
        ->and($chartArray['chart']['type'])->toBe('area')
        ->and($chartArray)->toHaveKey('stroke')
        ->and($chartArray['stroke'])->toHaveKey('curve')
        ->and($chartArray['stroke']['curve'])->toBe('straight')
        ->and($chart->colors->count())->toBe(1)
        ->and($chart->colors->first())->toBe(\App\Enums\Color::Green->rgb());
});

it('filters transactions to only include investments', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Create mixed transactions
    WalletTransaction::factory()->for($wallet)->create([
        'amount' => -10000, // $100.00 investment
        'is_investment' => true,
    ]);

    WalletTransaction::factory()->for($wallet)->create([
        'amount' => 5000, // $50.00 income
        'is_investment' => false,
    ]);

    WalletTransaction::factory()->for($wallet)->create([
        'amount' => -3000, // $30.00 expense
        'is_investment' => false,
    ]);

    // Act
    $component = Livewire::actingAs($user)->test(Investments::class);
    $chart = $component->instance()->chart();

    // The total should only include investment transactions
    expect($chart->total())->toBe(100.0)
        ->and($chart->total())->not->toBe(20.0); // 100 - 50 - 30 would be 20 if all transactions were included
});
