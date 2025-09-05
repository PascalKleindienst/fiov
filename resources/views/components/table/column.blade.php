@props(['text' => null])
<th {{ $attributes->merge(['class' => 'py-3 px-3 first:ps-0 last:pe-0 font-medium text-sm text-zinc-800 dark:text-white *:last:me-0']) }}>
    {{ $text ?? $slot }}
</th>
