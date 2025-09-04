<?php

declare(strict_types=1);

namespace App\Enums;

enum LicenseStatus
{
    case Valid;
    case Invalid;
    case No_License;
    case Unknown;

    public function hasNoLicense(): bool
    {
        return $this === self::No_License;
    }

    public function isValid(): bool
    {
        return $this === self::Valid;
    }

    public function isInvalid(): bool
    {
        return $this === self::Invalid;
    }

    public function isUnknown(): bool
    {
        return $this === self::Unknown;
    }
}
