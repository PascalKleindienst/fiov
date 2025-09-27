<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <x-toast />

        <flux:sidebar
            sticky
            collapsible
            class="border-e border-zinc-200 bg-zinc-50 px-0 data-flux-sidebar-collapsed-desktop:!px-0 dark:border-zinc-700 dark:bg-zinc-900"
        >
            <div class="in-data-flux-sidebar-collapsed-desktop:px-2">
                <flux:sidebar.header class="not-in-data-flux-sidebar-collapsed-desktop:px-4">
                    <flux:sidebar.brand :href="route('dashboard')" :name="config('app.name')">
                        <x-slot name="logo">
                            <x-app-logo-icon class="size-full" />
                        </x-slot>
                    </flux:sidebar.brand>
                    <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
                </flux:sidebar.header>
            </div>

            @persist('sidebar')
                <flux:sidebar.nav>
                    <flux:sidebar.item icon="home" :href="route('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="wallet" :href="route('wallets.index')" wire:current="wallets.*" wire:navigate>
                        {{ __('navigation.wallets') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="tags" :href="route('categories.index')" wire:current="categories.*" wire:navigate>
                        {{ __('navigation.categories') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item
                        icon="calendar-clock"
                        :href="route('recurring-transactions.index')"
                        wire:current="recurring-transactions.*"
                        wire:navigate
                    >
                        {{ __('navigation.recurring_transactions') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="banknotes" :href="route('budgets.index')" wire:current="budgets.*" wire:navigate>
                        {{ __('navigation.budgets') }}
                    </flux:sidebar.item>
                </flux:sidebar.nav>
            @endpersist

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.system')" wire:navigate wire.current="admin.*">
                    {{ __('navigation.settings') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="information-circle" href="#">Help</flux:sidebar.item>
            </flux:sidebar.nav>
        </flux:sidebar>

        <flux:header class="gap-4 border-b border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

            {{-- <div> --}}
            {{-- <flux:sidebar.search placeholder="Search..." /> --}}
            {{-- </div> --}}
            @include('partials.breadcrumbs')

            <flux:spacer />

            {{-- TODO: Notifications --}}
            {{-- <div> --}}
            {{-- <flux:menu.item --}}
            {{-- class="cursor-pointer" --}}
            {{-- x-data --}}
            {{-- x-on:click=" --}}
            {{-- $el.querySelector('button[disabled]') || --}}
            {{-- $dispatch('modal-show', { name: 'edit-profile' }) --}}
            {{-- " --}}
            {{-- data-flux-modal-trigger --}}
            {{-- > --}}
            {{-- <flux:icon icon="bell" color="gray" /> --}}
            {{-- <flux:badge>2</flux:badge> --}}
            {{-- <span class="sr-only">Notifications</span> --}}
            {{-- </flux:menu.item> --}}
            {{-- </div> --}}

            {{-- <flux:separator vertical subtle class="my-4" /> --}}

            {{-- User menu --}}
            <flux:dropdown position="top" align="end">
                <flux:profile avatar:size="sm" avatar:circle avatar:color="auto" :initials="auth()->user()->initials()" />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main>
            {{ $slot }}
        </flux:main>

        {{-- TODO: Add notifications --}}
        {{-- <flux:modal name="edit-profile" variant="flyout" position="right"> --}}
        {{-- <div class="space-y-6"> --}}
        {{-- <flux:heading size="xl">Notifications</flux:heading> --}}
        {{-- <div class="space-y-6 divide-y divide-zinc-200"> --}}
        {{-- @for ($i = 0; $i < 5; $i++) --}}
        {{-- <div class="pb-6"> --}}
        {{-- <flux:heading>User profile</flux:heading> --}}
        {{-- <flux:text class="mt-2">This information will be displayed publicly.</flux:text> --}}
        {{-- <flux:text>3min ago</flux:text> --}}
        {{-- </div> --}}
        {{-- @endfor --}}
        {{-- </div> --}}
        {{-- </div> --}}
        {{-- </flux:modal> --}}

        @fluxScripts
    </body>
</html>
