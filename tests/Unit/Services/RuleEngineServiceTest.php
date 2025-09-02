<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\RuleOperator;
use App\Models\User;
use App\Models\WalletCategory;
use App\Models\WalletCategoryRule;
use App\Services\RuleEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

it('returns null when no rules are matched', function (): void {
    $service = new RuleEngineService();
    $category = $service->apply(['description' => 'test']);

    expect($category)->toBeNull();
});

it('returns null when no rules fields are matched', function (): void {
    $category = WalletCategory::factory()->for($this->user)->create();
    WalletCategoryRule::factory()->for($category, 'category')->create([
        'field' => 'description',
        'operator' => RuleOperator::Contains,
        'value' => 'test',
    ]);
    $service = new RuleEngineService();
    $category = $service->apply(['does not exist' => 'not test']);

    expect($category)->toBeNull();
});

it('casts Money values to integers', function (): void {
    $category = WalletCategory::factory()->for($this->user)->create();
    WalletCategoryRule::factory()->create([
        'wallet_category_id' => $category->id,
        'field' => 'amount',
        'operator' => RuleOperator::Equals,
        'value' => '1234',
    ]);

    $service = new RuleEngineService();
    $matchedCategory = $service->apply(['amount' => money(1234)]);

    expect($matchedCategory)->not->toBeNull()
        ->and($matchedCategory->id)->toBe($category->id);
});

it('returns the correct category when a rule is matched', function (): void {
    $category = WalletCategory::factory()->for($this->user)->create();
    WalletCategoryRule::factory()->create([
        'wallet_category_id' => $category->id,
        'field' => 'description',
        'operator' => RuleOperator::Contains,
        'value' => 'test',
    ]);

    $service = new RuleEngineService();
    $matchedCategory = $service->apply(['description' => 'this is a test']);

    expect($matchedCategory)->not->toBeNull()
        ->and($matchedCategory->id)->toBe($category->id);
});

dataset('rule_operators', [
    ['operator' => RuleOperator::Equals, 'value' => 'test', 'data' => 'test', 'matches' => true],
    ['operator' => RuleOperator::Equals, 'value' => 'test', 'data' => 'Test', 'matches' => false],
    ['operator' => RuleOperator::NotEquals, 'value' => 'test', 'data' => 'something else', 'matches' => true],
    ['operator' => RuleOperator::Contains, 'value' => 'test', 'data' => 'this is a test', 'matches' => true],
    ['operator' => RuleOperator::NotContains, 'value' => 'test', 'data' => 'this is something else', 'matches' => true],
    ['operator' => RuleOperator::StartsWith, 'value' => 'test', 'data' => 'test this', 'matches' => true],
    ['operator' => RuleOperator::NotStartsWith, 'value' => 'test', 'data' => 'this test', 'matches' => true],
    ['operator' => RuleOperator::EndsWith, 'value' => 'test', 'data' => 'this is a test', 'matches' => true],
    ['operator' => RuleOperator::NotEndsWith, 'value' => 'test', 'data' => 'this is a tes', 'matches' => true],
    ['operator' => RuleOperator::GreaterThan, 'value' => 10, 'data' => 20, 'matches' => true],
    ['operator' => RuleOperator::LessThan, 'value' => 20, 'data' => 10, 'matches' => true],
    ['operator' => RuleOperator::GreaterThanOrEqual, 'value' => 10, 'data' => 10, 'matches' => true],
    ['operator' => RuleOperator::LessThanOrEqual, 'value' => 20, 'data' => 20, 'matches' => true],
]);

it('evaluates rule operator correctly', function (RuleOperator $operator, $value, $data, bool $matches): void {
    $category = WalletCategory::factory()->for($this->user)->create();
    WalletCategoryRule::factory()->create([
        'wallet_category_id' => $category->id,
        'field' => 'data',
        'operator' => $operator,
        'value' => $value,
    ]);

    $service = new RuleEngineService();
    $matchedCategory = $service->apply(['data' => $data]);

    if ($matches) {
        expect($matchedCategory)->not->toBeNull()
            ->and($matchedCategory?->id)->toBe($category?->id);
    } else {
        expect($matchedCategory)->toBeNull();
    }
})->with('rule_operators');
