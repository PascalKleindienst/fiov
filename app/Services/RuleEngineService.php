<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RuleOperator;
use App\Models\WalletCategory;
use App\Models\WalletCategoryRule;
use Cknow\Money\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use function array_key_exists;

final readonly class RuleEngineService
{
    /** @var Collection<int, WalletCategoryRule> */
    private Collection $rules;

    public function __construct()
    {
        $this->rules = Auth::user()?->walletCategoryRules()->get() ?? collect();
    }

    /**
     * @param  non-empty-array<string, mixed>  $data
     */
    public function apply(array $data): ?WalletCategory
    {
        foreach ($this->rules as $rule) {
            if (! array_key_exists($rule->field, $data)) {
                continue;
            }

            if ($this->isRuleMatched($rule, $data)) {
                return $rule->category;
            }
        }

        return null;
    }

    /**
     * @param  non-empty-array<string, mixed>  $data
     */
    private function isRuleMatched(WalletCategoryRule $rule, array $data): bool
    {
        $value = $data[$rule->field] ?? null;
        if ($value instanceof Money) {
            $value = (int) $value->getAmount();
        }

        return match ($rule->operator) {
            RuleOperator::Equals => $value === $rule->value,
            RuleOperator::NotEquals => $value !== $rule->value,
            RuleOperator::Contains => Str::contains((string) $value, (string) $rule->value, true),
            RuleOperator::NotContains => ! Str::contains((string) $value, (string) $rule->value, true),
            RuleOperator::StartsWith => Str::startsWith(Str::lower((string) $value), Str::lower((string) $rule->value)),
            RuleOperator::NotStartsWith => ! Str::startsWith(Str::lower((string) $value), Str::lower((string) $rule->value)),
            RuleOperator::EndsWith => Str::endsWith(Str::lower((string) $value), Str::lower((string) $rule->value)),
            RuleOperator::NotEndsWith => ! Str::endsWith(Str::lower((string) $value), Str::lower((string) $rule->value)),
            RuleOperator::GreaterThan => $value > (float) $rule->value,
            RuleOperator::LessThan => $value < (float) $rule->value,
            RuleOperator::GreaterThanOrEqual => $value >= (float) $rule->value,
            RuleOperator::LessThanOrEqual => $value <= (float) $rule->value,
        };
    }
}
