<template>
    <a :href="href" class="resource-finder__link resource-finder__link--category">
        <span class="resource-finder__link-icon" aria-hidden="true" v-html="icon"></span>
        <span class="resource-finder__link-text" v-html="name"></span>
    </a>
</template>

<script lang="ts">
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
        icon: {
            type: String,
            required: true,
        },
    },
    computed: {
        href(): string {
            return `${getRouteBaseByEnvironment()}#!/category/${this.id}`;
        },
    },
};
</script>
