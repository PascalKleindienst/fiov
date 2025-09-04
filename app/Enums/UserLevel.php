<?php

declare(strict_types=1);

namespace App\Enums;

enum UserLevel: string
{
    case Admin = 'admin';
    case User = 'user';

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }
}
