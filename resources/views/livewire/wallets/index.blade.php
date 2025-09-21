<div class="space-y-6">
    <x-sections.header
        :title="__('wallets.index')"
        :can:action="auth()->user()->can('create', App\Models\Wallet::class)"
        :action:text="__('wallets.create')"
        :action:href="route('wallets.create')"
        action:icon="plus"
    />

    <div class="flex justify-end">
        <flux:checkbox :label="__('wallets.show_archived')" wire:model.live="showArchived" />
    </div>

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
                    <flux:heading size="lg" class="flex items-center gap-2">
                        {{ $wallet->title }}
                        @if ($wallet->trashed())
                            <flux:tooltip :content="__('wallets.archived', ['name' => $wallet->title])">
                                <flux:icon name="archive" class="size-4" />
                            </flux:tooltip>
                        @endif
                    </flux:heading>
                    <flux:subheading class="hidden md:block">{{ $wallet->description }}</flux:subheading>
                </div>
                <div class="ml-auto">
                    <flux:dropdown>
                        <flux:button icon="ellipsis-vertical" variant="subtle" aria-label="{{ __('general.actions') }}" />
                        <flux:menu>
                            @can('update', $wallet)
                                <flux:menu.item :href="route('wallets.edit', $wallet)" wire:navigate icon="pencil">
                                    {{ __('general.edit') }}
                                </flux:menu.item>
                            @endif

                            @if ($wallet->trashed())
                                @can('restore', $wallet)
                                    <flux:menu.item icon="archive" wire:click="restoreWallet({{ $wallet->id }})">
                                        {{ __('wallets.reactivate') }}
                                    </flux:menu.item>
                                @endcan
                            @else
                                @can('delete', $wallet)
                                    <flux:menu.item icon="archive" wire:click="archiveWallet({{ $wallet->id }})">
                                        {{ __('wallets.archive') }}
                                    </flux:menu.item>
                                @endcan
                            @endif

                            @can('force-delete', $wallet)
                                <flux:modal.trigger name="confirm-deletion-{{ $wallet->id }}">
                                    <flux:menu.item variant="danger" icon="trash">
                                        {{ __('general.delete') }}
                                    </flux:menu.item>
                                </flux:modal.trigger>
                            @endcan
                        </flux:menu>
                    </flux:dropdown>

                    @can('force-delete', $wallet)
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
            <x-empty icon="wallet" :description="__('wallets.empty')" class="col-span-full" />
        @endforelse
    </div>

    {{ $wallets->links() }}
</div>
