import { default as ToastComponent, toast } from './components/Toast';

document.addEventListener('alpine:init', () => {
    Alpine.data('toast', ToastComponent);
});

window.$toast = toast;
