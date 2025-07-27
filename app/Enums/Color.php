<?php

declare(strict_types=1);

namespace App\Enums;

enum Color: string
{
    case Green = 'green';
    case Lime = 'lime';
    case Yellow = 'yellow';
    case Orange = 'orange';
    case Red = 'red';
    case Purple = 'purple';
    case Blue = 'blue';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function css(): string
    {
        return match ($this) {
            self::Blue => 'bg-sky-200 dark:bg-sky-600',
            self::Green => 'bg-emerald-200 dark:bg-emerald-600',
            self::Red => 'bg-rose-200 dark:bg-rose-600',
            self::Yellow => 'bg-yellow-200 dark:bg-yellow-600',
            self::Orange => 'bg-orange-200 dark:bg-orange-600',
            self::Purple => 'bg-purple-200 dark:bg-purple-600',
            self::Lime => 'bg-lime-200 dark:bg-lime-600',
        };
    }

    public function rgb(): string
    {
        return match ($this) {
            self::Blue => '#008FFB',
            self::Green => '#00E396',
            self::Red => '#FF4560',
            self::Yellow => '#f9ce1d',
            self::Orange => '#FF9800',
            self::Purple => '#775DD0',
            self::Lime => '#4ecdc4',
        };
    }
}
