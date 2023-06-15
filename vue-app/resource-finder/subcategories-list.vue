<template>
    <div class="resource-finder__list resource-finder__list--subcategories">
        <h2 class="resource-finder__list-heading" v-html="selectedCategoryName"></h2>
        <div class="resource-finder__list-inner">
            <a :href="backLinkHref" class="resource-finder__back-link">Learn on My Own</a>
            <ul class="resource-finder__list-items">
                <li
                    class="resource-finder__list-item"
                    :style="computeItemStyles(i)"
                    v-for="(subcategory, i) in subcategories"
                    :key="subcategory.id"
                >
                    <subcategories-list-item v-bind="subcategory"></subcategories-list-item>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import { state, getters } from "./store";
import { getRouteBaseByEnvironment } from "./util";
import SubcategoriesListItem from "./subcategories-list-item.vue";

interface Subcategory {
    id: string;
    name: string;
}

export default {
    components: {
        "subcategories-list-item": SubcategoriesListItem,
    },
    computed: {
        subcategories(): Subcategory[] {
            const ids = getters.topicIdsByParentId()[state.selectedCategoryId] || ([] as string[]);
            return ids.map((id) => {
                const topic = getters.topicsById()[id];
                return {
                    id: topic.id,
                    name: topic.name,
                };
            });
        },
        selectedCategoryName(): string {
            return getters.topicsById()[state.selectedCategoryId].name;
        },
        backLinkHref(): string {
            return `${getRouteBaseByEnvironment()}#!/`;
        },
    },
    methods: {
        computeItemStyles(index: number) {
            return {
                transitionDelay: `${(index + 1) * (0.5 / this.subcategories.length) + 0.2}s`,
            };
        },
    },
};
</script>
