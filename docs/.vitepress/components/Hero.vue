<script lang="ts" setup>
import { VPButton, VPImage } from 'vitepress/theme';
import { onMounted, ref } from 'vue';
import { useData } from 'vitepress';
import { computed } from '@vue/reactivity';
import { useTitle } from '../composables/typography';

const title = ref<HTMLElement>();
const { frontmatter } = useData();
const hero = computed(() => frontmatter.value.fiov_hero);

onMounted(() => {
    useTitle(title);
});
</script>

<template>
    <div v-if="hero" class="fiov-hero">
        <div class="fiov-hero__content">
            <span v-if="hero.subtitle" class="fiov-subtitle">{{ hero.subtitle }}</span>
            <h2 ref="title" class="fiov-title">{{ hero.title }}</h2>
            <p v-if="hero.lead" class="fiov-lead">{{ hero.lead }}</p>

            <div v-if="hero.actions" class="fiov-cta-wrapper">
                <VPButton
                    v-for="action in hero.actions"
                    :key="action.text"
                    :href="action.link"
                    :rel="action.rel"
                    :target="action.target"
                    :text="action.text"
                    :theme="action.theme"
                    size="big"
                    tag="a"
                />
            </div>
        </div>

        <div v-if="hero.image" class="fiov-hero__image">
            <VPImage :image="hero.image" />
        </div>
    </div>
</template>

<style lang="postcss" scoped>
.fiov-hero {
    margin-top: 2rem;
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 2rem;
    place-items: center;

    .fiov-hero__image {
        border-radius: 1rem;
        width: 100%;
        max-width: none;
        height: auto;
        box-shadow:
            0 20px 25px -5px rgb(0 0 0 / 0.1),
            0 8px 10px -6px rgb(0 0 0 / 0.1);

        &:deep(img) {
            border-radius: 1rem;
            border: 1px solid var(--vp-c-default-1);
        }
    }
}

.fiov-cta-wrapper {
    display: flex;
    gap: 1rem;
}
</style>
