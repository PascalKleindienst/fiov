<script lang="ts" setup>
import Features from '../components/Features.vue';
import Hero from '../components/Hero.vue';
</script>

<template>
    <div class="fiov">
        <Hero />

        <Features />
    </div>

    <slot />
</template>

<style lang="postcss" scoped>
.fiov {
    display: grid;
    gap: 8rem;
}
</style>
