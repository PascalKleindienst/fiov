<?php

declare(strict_types=1);

namespace App\Enums;

enum RecurringFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

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
            self::DAILY => __('recurring_transactions.frequencies.daily'),
            self::WEEKLY => __('recurring_transactions.frequencies.weekly'),
            self::MONTHLY => __('recurring_transactions.frequencies.monthly'),
            self::YEARLY => __('recurring_transactions.frequencies.yearly'),
        };
    }
}
