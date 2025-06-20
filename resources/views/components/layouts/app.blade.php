<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        @include('partials.breadcrumbs')

        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
