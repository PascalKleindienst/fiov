@php
    /** @var \App\Data\BreadcrumbItemData[] $breadcrumbs */
@endphp

@if ($breadcrumbs ?? false)
    <flux:breadcrumbs class="mb-6">
        <flux:breadcrumbs.item :href="route('dashboard')" :title="__('navigation.dashboard')" wire:navigate>
            <flux:icon name="home" aria-label="Home" />
        </flux:breadcrumbs.item>

        @foreach ($breadcrumbs as $breadcrumb)
            <flux:breadcrumbs.item :href="$breadcrumb->url" :title="$breadcrumb->title ?? null" wire:navigate>
                {{ $breadcrumb->title }}
            </flux:breadcrumbs.item>
        @endforeach
    </flux:breadcrumbs>
@endif
