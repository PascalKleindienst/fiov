<?php

declare(strict_types=1);

use App\Enums\RuleOperator;

it('returns the currency values', function (): void {
    expect(RuleOperator::values())
        ->toBeArray()
        ->toContain(
            'equals', 'not_equals',
            'contains', 'not_contains',
            'starts_with', 'not_starts_with',
            'ends_with', 'not_ends_with',
            'greater_than', 'less_than',
            'greater_than_or_equal', 'less_than_or_equal',
        );
});

it('shows a label', function (RuleOperator $operator): void {
    expect($operator->label())->toBeString();
})->with([
    [RuleOperator::Equals],
    [RuleOperator::NotEquals],
    [RuleOperator::Contains],
    [RuleOperator::NotContains],
    [RuleOperator::StartsWith],
    [RuleOperator::NotStartsWith],
    [RuleOperator::EndsWith],
    [RuleOperator::NotEndsWith],
    [RuleOperator::GreaterThan],
    [RuleOperator::LessThan],
    [RuleOperator::GreaterThanOrEqual],
    [RuleOperator::LessThanOrEqual],
]);
