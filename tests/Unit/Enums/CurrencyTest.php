<?php

declare(strict_types=1);

use App\Enums\Currency;

it('returns the correct symbol for the currency', function (Currency $currency, string $symbol): void {
    expect($currency->symbol())->toBe($symbol);
})->with([
    [Currency::USD, '$'],
    [Currency::EUR, '€'],
    [Currency::GBP, '£'],
    [Currency::ALL, 'L'],
    [Currency::AMD, '֏'],
    [Currency::AZN, '₼'],
    [Currency::BYN, 'Rbl'],
    [Currency::BAM, 'KM'],
    [Currency::BGN, 'лв.'],
    [Currency::CZK, 'Kč'],
    [Currency::DKK, 'kr.'],
    [Currency::GEL, '₾'],
    [Currency::HUF, 'Ft.'],
    [Currency::MKD, 'DEN'],
    [Currency::PLN, 'zł'],
    [Currency::RON, 'lei'],
    [Currency::RSD, 'DIN'],
    [Currency::TRY, '₺'],
    [Currency::UAH, '₴'],
]);
