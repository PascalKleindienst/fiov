@php
    /** @var \App\Data\BreadcrumbItemData[] $breadcrumbs */
@endphp

@if ($breadcrumbs ?? false)
    <flux:breadcrumbs class="hidden md:flex">
        <flux:breadcrumbs.item :href="route('dashboard')" wire:navigate>
            <flux:tooltip :content="__('navigation.dashboard')">
                <flux:icon name="home" aria-label="Home" />
            </flux:tooltip>
        </flux:breadcrumbs.item>

        @foreach ($breadcrumbs as $breadcrumb)
            <flux:breadcrumbs.item :href="$breadcrumb->url" :title="$breadcrumb->title ?? null" wire:navigate>
                {{ $breadcrumb->title }}
            </flux:breadcrumbs.item>
        @endforeach
    </flux:breadcrumbs>
@endif
