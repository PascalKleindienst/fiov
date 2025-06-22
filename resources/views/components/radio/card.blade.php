@props([
    'name' => $attributes->whereStartsWith('wire:model')->first(),
    'value',
    'checked' => false,
])
@php
    $id = uniqid('', true);
@endphp

<flux:field>
    <input @checked($checked) id="form-{{ $id }}-{{ $value }}" type="radio" name="{{ $name }}" value="{{ $value }}" class="peer sr-only" />
    <flux:label
        for="form-{{ $id }}-{{ $value }}"
        class="relative flex w-full cursor-pointer items-center gap-2 rounded border-2 border-zinc-200 p-2 peer-checked:border-accent peer-checked:font-bold peer-checked:text-accent peer-focus:ring-2 peer-focus:ring-zinc-800 peer-focus:ring-offset-2 peer-focus:outline-hidden dark:border-zinc-600"
    >
        {{ $slot }}
    </flux:label>
</flux:field>
