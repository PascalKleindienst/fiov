@persist('toast')
    <div x-data="toast">
        <div
            x-cloak
            x-show="toasts.length > 0"
            role="region"
            class="fixed z-50 flex w-full max-w-md cursor-auto gap-2 overflow-hidden"
            x-bind:class="{
                'flex-col-reverse': position === 'top-start' || position === 'top-end',
                'flex-col': position === 'bottom-start' || position === 'bottom-end',
                'top-4': position === 'top-end' || position === 'top-start',
                'bottom-4': position === 'bottom-end' || position === 'bottom-start',
                'end-4': position === 'top-end' || position === 'bottom-end',
                'start-4': position === 'top-start' || position === 'bottom-start',
            }"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div
                    role="alert"
                    class="flex items-center justify-between gap-4 rounded-lg border border-zinc-200/75 p-4 text-sm shadow-xs dark:border-zinc-700/75"
                    aria-atomic="true"
                    :aria-live="toast.variant === 'danger' ? 'assertive' : 'polite'"
                    x-show="toast.visible"
                    x-bind="transitionClasses"
                    x-transition:enter="transition duration-300 ease-out"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition duration-200 ease-in"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-bind:class="{
                        'bg-green-100 text-green-700 dark:bg-green-800 dark:text-green-100':
                            toast.variant === 'success',
                        'bg-rose-100 text-rose-700 dark:bg-rose-800 dark:text-rose-100':
                            toast.variant === 'danger',
                        'bg-amber-100 text-amber-700 dark:bg-amber-800 dark:text-amber-100':
                            toast.variant === 'warning',
                        'bg-white dark:bg-zinc-800': ! toast.variant,
                    }"
                >
                    <div class="shrink-0 self-center" x-cloak x-show="toast.variant">
                        <template x-if="toast.variant === 'success'">
                            <flux:icon icon="check" class="size-4 text-green-500" />
                        </template>
                        <template x-if="toast.variant === 'danger'">
                            <flux:icon icon="circle-x" class="size-4 text-red-600" />
                        </template>
                        <template x-if="toast.variant === 'warning'">
                            <flux:icon icon="triangle-alert" class="size-4 text-amber-600" />
                        </template>
                    </div>
                    <div class="flex-1">
                        <template x-if="toast.heading">
                            <flux:heading size="md" x-text="toast.heading" />
                        </template>
                        <flux:text x-text="toast.text" />
                    </div>
                    <div class="shrink-0">
                        <flux:button inset size="xs" variant="subtle" x-on:click="close(toast.id)">
                            <span class="sr-only">{{ __('Close') }}</span>
                            <flux:icon icon="x-mark" class="size-4" />
                        </flux:button>
                    </div>
                </div>
            </template>
        </div>
    </div>
@endpersist
