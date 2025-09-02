<?php

declare(strict_types=1);

it('returns the currency values', function (): void {
    expect(\App\Enums\RuleOperator::values())
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
