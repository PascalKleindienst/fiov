<?php

declare(strict_types=1);

use App\Actions\CreateOrUpdateCategory;
use App\Enums\Color;
use App\Enums\RuleOperator;
use App\Models\WalletCategory;
use App\Models\WalletCategoryRule;
use Illuminate\Support\Collection;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $this->action = new CreateOrUpdateCategory();
    $this->user = \App\Models\User::factory()->create();
    actingAs($this->user);
});

it('creates a new category', function (): void {
    $data = [
        'title' => 'Test Category',
        'color' => Color::Red,
        'icon' => \App\Enums\Icon::Money,
        'user_id' => $this->user->id,
    ];

    $this->action->handle($data);

    expect(WalletCategory::class)->databaseToHaveEncrypted([
        'title' => 'Test Category',
        'color' => Color::Red,
        'icon' => \App\Enums\Icon::Money,
        'user_id' => $this->user->id,
    ]);
});

it('updates an existing category', function (): void {
    $category = WalletCategory::factory()->create([
        'title' => 'Old Name',
        'color' => Color::Purple,
        'user_id' => $this->user->id,
    ]);

    $data = [
        'id' => $category->id,
        'title' => 'Updated Name',
        'color' => Color::Orange,
        'user_id' => $this->user->id,
    ];

    $this->action->handle($data);

    expect(WalletCategory::class)->databaseToHaveEncrypted([
        'id' => $category->id,
        'title' => 'Updated Name',
        'color' => Color::Orange,
        'user_id' => $this->user->id,
    ]);
});

it('creates new rules for a category', function (): void {
    $category = WalletCategory::factory()->for($this->user, 'user')->create();

    $rule = new WalletCategoryRule([
        'field' => 'description',
        'operator' => RuleOperator::Contains,
        'value' => 'test',
    ]);

    $rules = new Collection([$rule]);
    $this->action->handle($category->toArray(), $rules);

    expect(WalletCategoryRule::class)->databaseToHaveEncrypted([
        'wallet_category_id' => $category->id,
        'field' => 'description',
        'operator' => RuleOperator::Contains,
        'value' => 'test',
    ]);
});

it('updates existing rules and removes unused ones', function (): void {
    $category = WalletCategory::factory()
        ->for($this->user, 'user')
        ->has(WalletCategoryRule::factory()->count(2), 'rules')
        ->create();

    $existingRule = $category->rules->first();
    $updatedRule = new WalletCategoryRule([
        'id' => $existingRule->id,
        'field' => 'updated_field',
        'operator' => RuleOperator::Equals,
        'value' => 'updated_value',
    ]);

    $newRule = new WalletCategoryRule([
        'field' => 'new_field',
        'operator' => RuleOperator::NotEquals,
        'value' => 'new_value',
    ]);

    $rules = new Collection([$updatedRule, $newRule]);
    $this->action->handle(['id' => $category->id], $rules);

    expect(WalletCategoryRule::class)
        ->databaseToHaveEncrypted([
            'id' => $existingRule->id,
            'field' => 'updated_field',
            'operator' => RuleOperator::Equals,
            'value' => 'updated_value',
        ])
        ->databaseToHaveEncrypted([
            'wallet_category_id' => $category->id,
            'field' => 'new_field',
            'operator' => RuleOperator::NotEquals,
            'value' => 'new_value',
        ]);

    $this->assertDatabaseCount('wallet_category_rules', 2);
});

it('handles transaction rollback on failure', function (): void {
    $category = WalletCategory::factory()->create();

    $rule = new WalletCategoryRule([
        'field' => 'description',
        'operator' => RuleOperator::Equals,
        'value' => 'test',
    ]);

    $rules = new Collection([$rule]);

    try {
        $this->action->handle(['id' => $category->id], $rules);
        $this->fail('Expected an exception to be thrown');
    } catch (Throwable) {
        // Verify that no rules were created due to the transaction rollback
        $this->assertDatabaseCount('wallet_category_rules', 0);
    }
});

it('handles null rules collection', function (): void {
    $category = WalletCategory::factory()->for($this->user, 'user')->create();

    $this->action->handle(['id' => $category->id], null);

    $this->assertDatabaseHas('wallet_categories', ['id' => $category->id]);
    $this->assertDatabaseCount('wallet_category_rules', 0);
});
