@props([
    'value',
    'max' => 100,
    'min' => 0,
    'size' => 'sm',
    'label' => null,
    'color' => 'blue',
    'variant' => null,
])

@php
    $color = match ($variant) {
        'success' => 'green',
        'danger' => 'red',
        'warning' => 'yellow',
        'info' => 'blue',
        'secondary' => 'zinc',
        default => $color,
    };
    $percentage = ($value / $max) * 100;

    $colorClass = match ($color) {
        'blue' => 'bg-blue-600 text-white dark:bg-blue-700',
        'sky' => 'bg-sky-600 text-white dark:bg-sky-700',
        'red' => 'bg-red-600 text-white dark:bg-red-700',
        'orange' => 'bg-orange-600 text-white dark:bg-orange-700',
        'amber' => 'bg-amber-600 text-white dark:bg-amber-700',
        'yellow' => 'bg-yellow-600 text-white dark:bg-yellow-700',
        'lime' => 'bg-lime-600 text-white dark:bg-lime-700',
        'green' => 'bg-green-600 text-white dark:bg-green-700',
        'emerald' => 'bg-emerald-600 text-white dark:bg-emerald-700',
        'teal' => 'bg-teal-600 text-white dark:bg-teal-700',
        'cyan' => 'bg-cyan-600 text-white dark:bg-cyan-700',
        'indigo' => 'bg-indigo-600 text-white dark:bg-indigo-700',
        'violet' => 'bg-violet-600 text-white dark:bg-violet-700',
        'purple' => 'bg-purple-600 text-white dark:bg-purple-700',
        'fuchsia' => 'bg-fuchsia-600 text-white dark:bg-fuchsia-700',
        'pink' => 'bg-pink-600 text-white dark:bg-pink-700',
        'rose' => 'bg-rose-600 text-white dark:bg-rose-700',
        default => 'bg-zinc-600 text-white dark:bg-zinc-700',
    };

    $heightClass = match ($size) {
        'xs' => 'h-1',
        'sm' => 'h-1.5',
        'md' => 'h-2',
        'lg' => 'h-3',
        'xl' => 'h-4',
        '2xl' => 'h-5',
    };

    if (($label ?? false) && ! \in_array($size, ['xl', '2xl'])) {
        $heightClass = 'h-4';
    }
@endphp

<div
    class="{{ $heightClass }} flex w-full overflow-hidden rounded-full bg-gray-200 dark:bg-neutral-700"
    role="progressbar"
    aria-valuenow="{{ $value }}"
    aria-valuemin="{{ $min }}"
    aria-valuemax="{{ $max }}"
>
    <div
        class="{{ $colorClass }} flex flex-col justify-center overflow-hidden rounded-full text-center text-xs whitespace-nowrap transition-all duration-300 ease-in-out"
        style="width: {{ \Illuminate\Support\Number::percentage($percentage) }}"
    >
        @if ($label ?? false)
            {{ $label }}
        @else
            {{ $slot }}
        @endif
    </div>
</div>
