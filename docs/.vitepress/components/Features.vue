<script lang="ts" setup>
import FeatureItem from './FeatureItem.vue';
import { onMounted, ref } from 'vue';
import { useTitle } from '../composables/typography';
import { useData } from 'vitepress';
import { computed } from '@vue/reactivity';

const title = ref<HTMLElement>();
const { frontmatter } = useData();
const features = computed(() => frontmatter.value.fiov_features);

onMounted(() => {
    useTitle(title);
});
</script>

<template>
    <div v-if="features" class="fiov-features">
        <h2 ref="title" class="fiov-title">
            {{ features.title }}
        </h2>
        <p class="fiov-lead">{{ features.details }}</p>

        <div class="fiov-features-grid">
            <FeatureItem
                v-for="feature in features.items"
                :key="feature.title"
                :icon="feature.icon"
                :lead="feature.details"
                :link="feature.link"
                :linkText="feature.linkText"
                :title="feature.title"
            />
        </div>
    </div>
</template>

<style lang="postcss" scoped>
.fiov-features {
    text-align: center;
}

.fiov-features-grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}
</style>
