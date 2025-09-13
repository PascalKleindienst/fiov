<div class="space-y-6">
    <x-sections.header
        :title="__('categories.index')"
        :can:action="auth()->user()->can('create', App\Models\WalletCategory::class)"
        :action:text="__('categories.create')"
        :action:href="route('categories.create')"
        action:icon="plus"
    />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        @forelse ($this->categories as $category)
            <div class="flex items-center gap-4 rounded border border-zinc-200 p-4 dark:border-zinc-600">
                <flux:icon
                    :name="$category->icon->value ?? 'wallet'"
                    class="{{ $category->color?->css() ?? 'bg-zinc-200 dark:bg-zinc-600' }} size-12 rounded p-2"
                />
                <flux:heading size="lg">{{ $category->title }}</flux:heading>
                <div class="ml-auto flex gap-2">
                    @can('update', $category)
                        <flux:button
                            wire:navigate
                            :href="route('categories.edit', $category)"
                            :title="__('categories.edit', ['name' => $category->title])"
                            icon="pencil"
                        ></flux:button>
                    @endcan

                    @can('delete', $category)
                        <flux:modal.trigger name="confirm-category-deletion-{{ $category->id }}">
                            <flux:button
                                :title="__('categories.delete', ['name' => $category->title])"
                                variant="danger"
                                class="cursor-pointer"
                                icon="trash"
                            />
                        </flux:modal.trigger>

                        <flux:modal name="confirm-category-deletion-{{ $category->id }}" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
                            <form wire:submit="deleteCategory({{ $category->id }})" class="space-y-6">
                                <div>
                                    <flux:heading size="lg">{{ __('categories.confirm_delete') }}</flux:heading>

                                    <flux:subheading>
                                        {{ __('categories.confirm_delete_desc') }}
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
            <x-empty icon="tags" description="{{ __('categories.empty') }}" class="col-span-full">
                <flux:button variant="primary" :href="route('categories.create')" wire:navigate>
                    {{ __('categories.create') }}
                </flux:button>
            </x-empty>
        @endforelse
    </div>
    {{ $this->categories->links() }}
</div>
