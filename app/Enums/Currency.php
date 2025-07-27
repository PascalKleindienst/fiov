<?php

declare(strict_types=1);

namespace App\Enums;

enum Currency: string
{
    case EUR = 'EUR';
    case GBP = 'GBP';
    case USD = 'USD';
    case CAD = 'CAD';

    case ALL = 'ALL';
    case AMD = 'AMD';
    case AZN = 'AZN';
    case BYN = 'BYN';
    case BAM = 'BAM';
    case BGN = 'BGN';
    case CZK = 'CZK';
    case DKK = 'DKK';
    case GEL = 'GEL';
    case HUF = 'HUF';
    case ISK = 'ISK';
    case CHF = 'CHF';
    case MDL = 'MDL';
    case NOK = 'NOK';
    case PLN = 'PLN';
    case RON = 'RON';
    case RSD = 'RSD';
    case SEK = 'SEK';
    case TRY = 'TRY';
    case UAH = 'UAH';
    case MKD = 'MKD';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function symbol(): string
    {
        return match ($this) {
            self::USD, self::CAD => '$',
            self::EUR => '€',
            self::GBP => '£',

            self::ALL, self::MDL => 'L',
            self::AMD => '֏',
            self::AZN => '₼',
            self::BYN => 'Rbl',
            self::BAM => 'KM',
            self::BGN => 'лв.',
            self::CZK => 'Kč',
            self::DKK, self::ISK, self::NOK, self::SEK => 'kr.',
            self::GEL => '₾',
            self::HUF => 'Ft.',
            self::MKD => 'DEN',
            self::PLN => 'zł',
            self::RON => 'lei',
            self::RSD => 'DIN',
            self::TRY => '₺',
            self::UAH => '₴',
            default => $this->name,
        };
    }
}
