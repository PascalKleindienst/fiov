@props([
    'title',
    'label',
])
<div {{ $attributes }}>
    @if ($title)
        <flux:heading class="text-sm font-semibold">{{ $title }}</flux:heading>
    @endif

    <flux:text>
        @if ($label ?? false)
            {{ $label }}
        @else
            {{ $slot }}
        @endif
    </flux:text>
</div>
