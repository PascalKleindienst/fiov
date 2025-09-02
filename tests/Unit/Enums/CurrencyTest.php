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

it('returns the currency values', function (): void {
    expect(Currency::values())
        ->toBeArray()
        ->toContain(
            'EUR', 'GBP', 'USD', 'CAD', 'ALL', 'AMD', 'AZN', 'BYN', 'BAM', 'BGN', 'CZK', 'DKK', 'GEL', 'HUF', 'MKD', 'PLN', 'RON', 'RSD', 'TRY', 'UAH', 'MDL', 'NOK', 'SEK', 'CHF', 'ISK',
        );
});
