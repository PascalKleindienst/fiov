<script lang="ts" setup>
import { VPLink } from 'vitepress/theme';
import Icon from './Icon.vue';

interface Props {
    title: string;
    lead: string;
    icon?: 'lucide-tablet-smartphone' | 'lucide-wallet' | 'lucide-chart-no-axes-combined' | 'lucide-arrow-right-left' | 'lucide-tags';
    link?: string;
    linkText?: string;
}
const props = defineProps<Props>();
</script>

<template>
    <div class="fiov-feature">
        <div v-if="props.icon" class="fiov-feature-icon">
            <Icon :icon="props.icon" />
        </div>
        <div class="fiov-feature-content">
            <h3 class="fiov-feature-title">{{ props.title }}</h3>
            <p class="fiov-feature-lead">{{ props.lead }}</p>

            <VPLink v-if="props.link" :href="props.link">{{ props.linkText }}</VPLink>
        </div>
    </div>
</template>

<style lang="postcss">
.fiov-feature {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    text-align: start;

    .fiov-feature-title {
        margin: 0;
    }

    .fiov-feature-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        background-color: var(--vp-button-brand-bg);
        color: var(--vp-button-brand-text);
        padding: 10px;
    }
}
</style>
