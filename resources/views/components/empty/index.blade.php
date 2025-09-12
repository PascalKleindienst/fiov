@props(['icon' => null, 'title' => null, 'description' => null])
<x-card
    {{ $attributes->merge(['class' => 'space-y-4 border-2 border-dashed py-12 text-center shadow-none hover:border-zinc-400 dark:border-white/20']) }}
>
    @if ($icon)
        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-600">
            <flux:icon :name="$icon" class="size-12" />
        </div>
    @endif

    @if ($title)
        <flux:heading size="xl">{{ $title }}</flux:heading>
    @endif

    @if ($description)
        <flux:text size="xl">{{ $description }}</flux:text>
    @endif

    {{ $slot }}
</x-card>
