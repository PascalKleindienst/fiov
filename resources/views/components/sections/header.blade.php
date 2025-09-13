@php
    $actionText ??= $attributes->pluck('action:text');
    $canAction ??= $attributes->pluck('can:action');
    $actionHref ??= $attributes->pluck('action:href');
    $actionVariant ??= $attributes->pluck('action:variant');
    $actionIcon ??= $attributes->pluck('action:icon');
@endphp

@props([
    'title' => null,
    'lead' => null,
    'canAction' => true,
    'actionText' => null,
    'actionHref' => null,
    'actionVariant' => 'primary',
    'actionIcon' => null,
])
<div class="flex-wrap lg:flex lg:items-center lg:justify-between">
    <div class="min-w-0 flex-1">
        @if ($title)
            <flux:heading size="xl" level="2">{{ $title }}</flux:heading>
        @endif

        @if ($lead)
            <flux:subheading size="lg" class="mt-2">{{ $lead }}</flux:subheading>
        @endif

        {{ $slot }}
    </div>

    <div class="mt-5 flex gap-4 lg:mt-0 lg:ml-4">
        @if ($actionText && $canAction)
            <flux:button :variant="$actionVariant" :href="$actionHref" wire:navigate :icon="$actionIcon">
                {{ $actionText }}
            </flux:button>
        @endif

        @if (isset($actions))
            {{ $actions }}
        @endif
    </div>

    <flux:separator class="flex-fill mt-4" />
</div>
