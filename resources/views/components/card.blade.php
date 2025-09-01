<div
    {{ $attributes->merge(['class' => 'bg-white dark:bg-white/10 border border-zinc-200 dark:border-white/10 [:where(&)]:p-6 [:where(&)]:rounded-xl shadow']) }}
>
    {{ $slot }}
</div>
