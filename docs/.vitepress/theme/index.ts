// .vitepress/theme/index.ts or .vitepress/theme/index.js
import Theme from 'vitepress/theme';

import 'vitepress-markdown-timeline/dist/theme/index.css';

export default {
    ...Theme,
    enhanceApp(ctx) {
        Theme.enhanceApp(ctx);
    }
};
