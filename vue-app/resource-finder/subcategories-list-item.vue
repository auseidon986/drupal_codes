<template>
    <a :href="href" class="resource-finder__link resource-finder__link--subcategory">
        <span class="resource-finder__link-text" v-html="name"></span>
    </a>
</template>

<script lang="ts">
import { state } from "./store";
import { getRouteBaseByEnvironment } from "./util";

export default {
    props: {
        id: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
    },
    computed: {
        href(): string {
            return `${getRouteBaseByEnvironment()}#!/category/${state.selectedCategoryId}/subcategory/${this.id}`;
        },
    },
};
</script>
