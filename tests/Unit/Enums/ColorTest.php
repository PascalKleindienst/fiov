<?php

use App\Enums\Color;

it('returns the correct css color class', function (Color $color, $css) {
    expect($color->css())->toBe($css);
})->with([
    [Color::Blue, 'bg-sky-200'],
    [Color::Green, 'bg-emerald-200'],
    [Color::Red, 'bg-rose-200'],
    [Color::Yellow, 'bg-yellow-200'],
    [Color::Orange, 'bg-orange-200'],
    [Color::Purple, 'bg-purple-200'],
    [Color::Lime, 'bg-lime-200'],
]);
