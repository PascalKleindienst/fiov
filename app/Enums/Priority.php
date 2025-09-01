<?php

declare(strict_types=1);

namespace App\Enums;

enum Priority: int
{
    case Low = 10;
    case Medium = 20;
    case High = 30;

    public function label(): string
    {
        return match ($this) {
            self::Low => __('general.priority.low'),
            self::Medium => __('general.priority.medium'),
            self::High => __('general.priority.high'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'text-sky-600',
            self::Medium => 'text-amber-600',
            self::High => 'text-red-600',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Low => 'circle-small',
            self::Medium => 'circle-dot',
            self::High => 'flame',
        };
    }
}
