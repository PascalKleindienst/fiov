<?php

declare(strict_types=1);

use App\Enums\Color;

it('returns the correct css color class', function (Color $color, $css): void {
    expect($color->css())->toBe($css);
})->with([
    [Color::Blue, 'bg-sky-200 dark:bg-sky-600'],
    [Color::Green, 'bg-emerald-200 dark:bg-emerald-600'],
    [Color::Red, 'bg-rose-200 dark:bg-rose-600'],
    [Color::Yellow, 'bg-yellow-200 dark:bg-yellow-600'],
    [Color::Orange, 'bg-orange-200 dark:bg-orange-600'],
    [Color::Purple, 'bg-purple-200 dark:bg-purple-600'],
    [Color::Lime, 'bg-lime-200 dark:bg-lime-600'],
]);

it('returns the correct rgb value', function (Color $color): void {
    expect($color->rgb())->toBeHexColor();
})->with([
    [Color::Blue],
    [Color::Green],
    [Color::Red],
    [Color::Yellow],
    [Color::Orange],
    [Color::Purple],
    [Color::Lime],
]);
