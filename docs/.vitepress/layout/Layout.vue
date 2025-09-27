<script setup>
import DefaultTheme from 'vitepress/theme';
import Sponsors from '../components/Sponsors.vue';

const { Layout: BaseLayout } = DefaultTheme;
</script>

<template>
    <BaseLayout>
        <template #aside-outline-after>
            <Sponsors />
        </template>
    </BaseLayout>
</template>
