import { Theme } from 'vitepress';
import DefaultTheme from 'vitepress/theme';
import CustomHomepage from '../layout/CustomHomepage.vue';
import Layout from '../layout/Layout.vue';
import './custom.pcss';

export default {
    Layout,
    extends: DefaultTheme,
    enhanceApp(ctx) {
        DefaultTheme.enhanceApp(ctx);
        ctx.app.component('custom-homepage', CustomHomepage);
    }
} satisfies Theme;
