<?php

declare(strict_types=1);

use App\Livewire\Charts\Savings;
use App\Models\User;
use App\Models\Wallet;
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

it('shows the savings chart for the authenticated user', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act & Assert
    Livewire::actingAs($user)->test(Savings::class)
        ->assertViewIs('livewire.charts.chart')
        ->assertSeeText(__('charts.savings'))
        ->assertOk();
});

it('displays all transactions accumulated by time interval', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Create transactions with different amounts
    WalletTransaction::factory()->for($wallet)->create([
        'amount' => 5000, // $50.00 income
    ]);

    WalletTransaction::factory()->for($wallet)->create([
        'amount' => -2000, // $20.00 expense
    ]);

    // Act
    $component = Livewire::actingAs($user)->test(Savings::class);

    // Assert
    $component->assertViewIs('livewire.charts.chart');

    $chart = $component->instance()->chart();

    expect($chart->name)->toBe(__('charts.savings'))
        ->and($chart->currency)->toBe($wallet->currency->value)
        ->and($chart->total())->toBe(30.0)
        ->and($chart->series->first()['data'] ?? [])->not->toBeEmpty();
});

it('can change the interval for the chart', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(Savings::class);

    // Assert
    expect($component->instance()->interval)->toBe('year');

    // Change interval to MONTH
    $component->set('interval', 'month')
        ->assertSet('interval', 'month');

    // Chart should be recomputed with new interval
    $chart = $component->instance()->chart();
    expect($chart)->toBeInstanceOf(\App\Data\Chart::class);
});

it('has area chart options', function (): void {
    // Arrange
    $user = User::factory()->create();
    $wallet = Wallet::factory()->for($user, 'user')->create();

    // Act
    $component = Livewire::actingAs($user)->test(Savings::class);
    $chart = $component->instance()->chart();

    // Assert
    $chartArray = $chart->toArray();
    expect($chartArray)->toHaveKey('chart')
        ->and($chartArray['chart'])->toHaveKey('type')
        ->and($chartArray['chart']['type'])->toBe('area')
        ->and($chartArray)->toHaveKey('stroke')
        ->and($chartArray['stroke'])->toHaveKey('curve')
        ->and($chartArray['stroke']['curve'])->toBe('straight');
});
