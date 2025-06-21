type position = 'top-start' | 'top-end' | 'bottom-start' | 'bottom-end';
type variant = null | 'success' | 'danger' | 'warning';

type FluxToast = {
    duration: number;
    slots: {
        heading?: string;
        text: string;
    };
    dataset: {
        variant?: variant;
    };
};

type Toast = {
    id: number;
    visible: boolean;
    heading: string | null;
    text: string;
    variant: variant;
};

type ToastOptions = Omit<Toast, 'id' | 'visible'> & {
    position?: position;
    duration?: number;
};

export default () => ({
    // Options
    position: 'top-end' as position,
    duration: 5000,
    variant: 'success' as variant,

    // State
    toasts: [] as Toast[],
    nextId: 1,

    init() {
        document.addEventListener('keydown', (t) => {
            if (t.key === 'Escape') {
                this.close(this.toasts[this.toasts.length - 1].id);
            }
        });

        document.addEventListener('toast-show', (e: CustomEvent<FluxToast>) => {
            this.show(e.detail);
        });
    },

    show(toast: FluxToast) {
        const id = this.nextId++;

        this.toasts.push({
            id,
            heading: toast.slots?.heading,
            text: toast.slots.text,
            variant: toast.dataset?.variant || null
        });

        setTimeout(() => {
            const index = this.toasts.findIndex((toast: Toast) => toast.id === id);

            if (index > -1) {
                this.toasts[index].visible = true;
            }
        }, 30);

        if (toast.duration) {
            setTimeout(() => {
                this.close(id);
            }, toast.duration);
        }
    },

    close(id: number) {
        const index = this.toasts.findIndex((toast: Toast) => toast.id === id);

        if (index > -1) {
            this.toasts[index].visible = false;

            setTimeout(() => {
                this.toasts.splice(index, 1);
            }, 300);
        }
    },

    transitionClasses: {
        'x-transition:enter-start'() {
            if (this.position === 'top-start' || this.position === 'bottom-start') {
                return 'opacity-0 -translate-x-12 rtl:translate-x-12';
            } else if (this.position === 'top-end' || this.position === 'bottom-end') {
                return 'opacity-0 translate-x-12 rtl:-translate-x-12';
            }
        },
        'x-transition:leave-end'() {
            if (this.position === 'top-start' || this.position === 'bottom-start') {
                return 'opacity-0 -translate-x-12 rtl:translate-x-12';
            } else if (this.position === 'top-end' || this.position === 'bottom-end') {
                return 'opacity-0 translate-x-12 rtl:-translate-x-12';
            }
        }
    }
});

export const toast = (toast: string | ToastOptions) => {
    if (typeof toast === 'string') {
        document.dispatchEvent(
            new CustomEvent('toast-show', {
                detail: {
                    duration: 5000,
                    slots: {
                        text: toast
                    }
                }
            })
        );
    } else {
        document.dispatchEvent(
            new CustomEvent('toast-show', {
                detail: {
                    duration: toast.duration,
                    dataset: {
                        variant: toast.variant
                    },
                    slots: {
                        heading: toast.heading,
                        text: toast.text
                    }
                }
            })
        );
    }
};
