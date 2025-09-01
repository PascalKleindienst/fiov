<?php

declare(strict_types=1);

namespace App\Enums;

enum BudgetStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Paused = 'paused';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(static fn (self $type) => $type->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::Active => __('budgets.status.active'),
            self::Completed => __('budgets.status.completed'),
            self::Cancelled => __('budgets.status.cancelled'),
            self::Paused => __('budgets.status.paused'),
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    public function isPaused(): bool
    {
        return $this === self::Paused;
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'sky',
            self::Completed => 'green',
            self::Cancelled => 'red',
            self::Paused => 'yellow',
        };
    }
}
