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

    public function css(): string
    {
        return match ($this) {
            self::Blue => 'bg-sky-200',
            self::Green => 'bg-emerald-200',
            self::Red => 'bg-rose-200',
            self::Yellow => 'bg-yellow-200',
            self::Orange => 'bg-orange-200',
            self::Purple => 'bg-purple-200',
            self::Lime => 'bg-lime-200',
        };
    }
}
