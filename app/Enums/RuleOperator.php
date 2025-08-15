<?php

declare(strict_types=1);

namespace App\Enums;

enum RuleOperator: string
{
    case Equals = 'equals';
    case NotEquals = 'notEquals';
    case Contains = 'contains';
    case NotContains = 'notContains';
    case StartsWith = 'startsWith';
    case NotStartsWith = 'notStartsWith';
    case EndsWith = 'endsWith';
    case NotEndsWith = 'notEndsWith';
    case GreaterThan = 'greaterThan';
    case LessThan = 'lessThan';
    case GreaterThanOrEqual = 'greaterThanOrEqual';
    case LessThanOrEqual = 'lessThanOrEqual';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Equals => __('general.operator.equals'),
            self::NotEquals => __('general.operator.not_equal'),
            self::Contains => __('general.operator.contains'),
            self::NotContains => __('general.operator.not_contains'),
            self::StartsWith => __('general.operator.starts_with'),
            self::NotStartsWith => __('general.operator.not_starts_with'),
            self::EndsWith => __('general.operator.ends_with'),
            self::NotEndsWith => __('general.operator.not_ends_with'),
            self::GreaterThan => __('general.operator.greater_than'),
            self::LessThan => __('general.operator.less_than'),
            self::GreaterThanOrEqual => __('general.operator.greater_than_or_equal'),
            self::LessThanOrEqual => __('general.operator.less_than_or_equal'),
        };
    }
}
