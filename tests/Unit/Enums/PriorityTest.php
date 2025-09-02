<?php

declare(strict_types=1);

use App\Enums\Priority;

it('returns the correct css color class', function (Priority $prio, string $expected): void {
    expect($prio->color())->toBe($expected);
})->with([
    [Priority::Low, 'text-sky-600'],
    [Priority::Medium, 'text-amber-600'],
    [Priority::High, 'text-red-600'],
]);

it('returns the correct icon', function (Priority $prio, string $expected): void {
    expect($prio->icon())->toBe($expected);
})->with([
    [Priority::Low, 'circle-small'],
    [Priority::Medium, 'circle-dot'],
    [Priority::High, 'flame'],
]);
