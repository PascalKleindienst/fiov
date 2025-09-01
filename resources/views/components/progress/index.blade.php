@props([
    'title' => null,
    'label' => null,
])
@php
    $labelPosition = $labelPosition ?? $attributes->pluck('label:position');

    if (isset($slot) && $slot->hasActualContent()) {
        $label = $slot;
    }
@endphp

<div class="flex items-center gap-x-3 whitespace-nowrap">
    @if ($labelPosition === 'start')
        <x-progress.label class="w-10 text-start" :title="$title" :label="$label" />
    @endif

    @if ($title)
        <div class="flex-1">
            <x-progress.label class="mb-2 flex items-center justify-between" :title="$title" :label="$label" />
            <x-progress.bar :attributes="$attributes" />
        </div>
    @else
        <x-progress.bar :attributes="$attributes" :label="$label" />
    @endif

    @if ($labelPosition === 'end')
        <x-progress.label class="w-10 text-end" :title="$title" :label="$label" />
    @endif
</div>
