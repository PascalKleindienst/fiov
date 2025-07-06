import Chart from './components/Chart.js';
import { default as ToastComponent, toast } from './components/Toast';

document.addEventListener('alpine:init', () => {
    Alpine.data('toast', ToastComponent);

    Alpine.data('chart', Chart);
});

window.$toast = toast;
