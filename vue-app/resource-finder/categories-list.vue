<template>
    <div class="resource-finder__list resource-finder__list--categories">
        <h2 class="resource-finder__list-heading">
            What Are You Interested In?
        </h2>
        <div class="resource-finder__list-inner">
            <ul class="resource-finder__list-items">
                <li
                    class="resource-finder__list-item"
                    :style="computeItemStyles(i)"
                    v-for="(category, i) in categories"
                    :key="category.id"
                >
                    <categories-list-item v-bind="category"></categories-list-item>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import { getters } from "./store";
import CategoriesListItem from "./categories-list-item.vue";

interface Category {
    id: string;
    name: string;
    icon: string;
}

export default {
    components: {
        "categories-list-item": CategoriesListItem,
    },
    computed: {
        categories(): Category[] {
            const ids = getters.topicIdsByParentId()["0"] || ([] as string[]);
            return ids.map((id) => {
                const topic = getters.topicsById()[id];
                return {
                    id: topic.id,
                    name: topic.name,
                    icon: topic.svg_icon ? topic.svg_icon : "",
                };
            });
        },
    },
    methods: {
        computeItemStyles(index: number) {
            return {
                transitionDelay: `${(index + 1) * (0.5 / this.categories.length) + 0.2}s`,
            };
        },
    },
};
</script>
