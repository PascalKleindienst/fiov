<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        @include('partials.breadcrumbs')

        <section class="w-full">
            @if (isset($heading) && $heading->isNotEmpty())
                <div class="relative mb-6 w-full">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        {{ $heading }}
                    </div>
                    <flux:separator variant="subtle" />
                </div>
            @endif

            <div class="flex items-start gap-4 max-md:flex-col">
                <div class="me-10 w-full pb-4 md:w-[220px]">
                    <flux:navlist>
                        @can('viewAny', \App\Models\User::class)
                            <flux:navlist.item :href="route('admin.users')" wire:navigate :current="request()->routeIs('admin.users')">
                                {{ __('users.index') }}
                            </flux:navlist.item>
                        @endcan

                        <flux:navlist.item :href="route('admin.system')" wire:navigate :current="request()->routeIs('admin.system')">
                            {{ __('system.index') }}
                        </flux:navlist.item>
                    </flux:navlist>
                </div>

                <flux:separator class="md:hidden" />

                <div class="flex-1 self-stretch max-md:pt-6">
                    {{ $slot }}
                </div>
            </div>
        </section>
    </flux:main>
</x-layouts.app.sidebar>
