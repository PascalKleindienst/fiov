<div class="space-y-6">
    <x-sections.header
        :title="__('wallets.index')"
        :can:action="auth()->user()->can('create', App\Models\Wallet::class)"
        :action:text="__('wallets.create')"
        :action:href="route('wallets.create')"
        action:icon="plus"
    />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 2xl:grid-cols-3">
        @forelse ($wallets as $wallet)
            @php
                /** @var \App\Models\Wallet $wallet */
            @endphp

            <div class="flex items-center gap-4 rounded border border-zinc-200 p-4 dark:border-zinc-600">
                {{-- <flux:icon :name="$wallet->icon" class="size-12 rounded bg-yellow-200 p-2" /> --}}
                <flux:icon
                    :name="$wallet->icon->value ?? 'wallet'"
                    class="{{ $wallet->color?->css() ?? 'bg-zinc-200 dark:bg-zinc-600' }} size-12 rounded p-2"
                />
                <div>
                    <flux:heading size="lg">{{ $wallet->title }}</flux:heading>
                    <flux:subheading class="hidden md:block">{{ $wallet->description }}</flux:subheading>
                </div>
                <div class="ml-auto flex gap-2">
                    @can('update', $wallet)
                        <flux:button
                            wire:navigate
                            :title="__('wallets.edit', ['name' => $wallet->title])"
                            :href="route('wallets.edit', $wallet)"
                            icon="pencil"
                        ></flux:button>
                    @endcan

                    @can('delete', $wallet)
                        <flux:modal.trigger name="confirm-deletion-{{ $wallet->id }}">
                            <flux:button
                                :title="__('wallets.delete', ['name' => $wallet->title])"
                                variant="danger"
                                class="cursor-pointer"
                                icon="trash"
                            ></flux:button>
                        </flux:modal.trigger>

                        <flux:modal name="confirm-deletion-{{ $wallet->id }}" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
                            <form wire:submit="deleteWallet({{ $wallet->id }})" class="space-y-6">
                                <div>
                                    <flux:heading size="lg">{{ __('wallets.confirm_delete') }}</flux:heading>

                                    <flux:subheading>
                                        {{ __('wallets.confirm_delete_desc') }}
                                    </flux:subheading>
                                </div>

                                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                    <flux:modal.close>
                                        <flux:button variant="filled">{{ __('general.cancel') }}</flux:button>
                                    </flux:modal.close>

                                    <flux:button variant="danger" type="submit">
                                        {{ __('general.delete') }}
                                    </flux:button>
                                </div>
                            </form>
                        </flux:modal>
                    @endcan
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center gap-4 rounded border border-dashed border-zinc-200 p-4 dark:border-zinc-600">
                <flux:heading size="md" class="text-zinc-600 dark:text-zinc-400">
                    {{ __('wallets.empty') }}
                </flux:heading>
            </div>
        @endforelse
    </div>

    {{ $wallets->links() }}
</div>
