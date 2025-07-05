import { toast } from '@/components/Toast';
import type { Alpine } from 'alpinejs';

declare global {
    interface Window {
        Alpine: Alpine;
        $toast: typeof toast;
    }

    const Alpine: Alpine;
}
