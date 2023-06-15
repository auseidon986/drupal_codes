<template>
    <div class="resource-finder__list resource-finder__list--terms">
        <h2 class="resource-finder__list-heading" v-html="selectedSubcategoryName"></h2>
        <div class="resource-finder__list-inner">
            <a :href="backLinkHref" class="resource-finder__back-link" v-html="selectedCategoryName"></a>
            <ul class="resource-finder__list-items">
                <li
                    class="resource-finder__list-item"
                    :style="computeItemStyles(i)"
                    v-for="(term, i) in terms"
                    :key="term.id"
                >
                    <terms-list-item v-bind="term"></terms-list-item>
                </li>
            </ul>
        </div>
    </div>
</template>

<script lang="ts">
import { state, getters } from "./store";
import { getRouteBaseByEnvironment } from "./util";
import TermsListItem from "./terms-list-item.vue";

interface Term {
    id: string;
    name: string;
    url: string;
    total: number;
    last: boolean;
}

export default {
    components: {
        "terms-list-item": TermsListItem,
    },
    computed: {
        selectedCategoryName(): string {
            let parentCategoryId = state.selectedCategoryId;
            if (state.parentSubcategoryIds.length > 0) {
                parentCategoryId = state.parentSubcategoryIds.slice(-1)[0];
            }

            const selectedCategory = getters.topicsById()[parentCategoryId];
            if (!!selectedCategory) {
                return selectedCategory.name;
            } else {
                return "";
            }
        },
        selectedSubcategoryName(): string {
            const selectedSubcategory = getters.topicsById()[state.selectedSubcategoryId];
            if (!!selectedSubcategory) {
                return selectedSubcategory.name;
            } else {
                return "";
            }
        },
        terms(): Term[] {
            const ids = getters.topicIdsByParentId()[state.selectedSubcategoryId] || ([] as string[]);

            return ids
                .map((id) => {
                    const topic = getters.topicsById()[id];
                    const hasChildren = getters.hasChildren(id);
                    const parentPaths = [...state.parentSubcategoryIds, state.selectedSubcategoryId, id];
                    const subUrl = `${getRouteBaseByEnvironment()}#!/category/${state.selectedCategoryId}/subcategory/${parentPaths.join('/')}`;
                    return {
                        id: topic.id,
                        name: topic.name,
                        // total: +topic.publication_ids + +topic.resource_ids,
                        total: +topic.resource_ids,
                        last: !hasChildren,
                        url: hasChildren ? subUrl : topic.url,
                    };
                })
                .filter((result) => {
                    return true || result.total > 0;
                });
        },
        backLinkHref(): string {
            if (state.parentSubcategoryIds.length > 0) {
                const parentId = state.parentSubcategoryIds.slice(-1)[0];
                return `${getRouteBaseByEnvironment()}#!/category/${state.selectedCategoryId}/subcategory/${parentId}`;
            } else {
                return `${getRouteBaseByEnvironment()}#!/category/${state.selectedCategoryId}`;
            }
        },
    },
    methods: {
        computeItemStyles(index: number) {
            return {
                transitionDelay: `${index * (0.5 / this.terms.length) + 0.2}s`,
            };
        },
    },
};
</script>
