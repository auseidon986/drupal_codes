<template>
    <a :href="url" class="resource-finder__link resource-finder__link--term">
        <span class="resource-finder__link-text" v-html="name"></span>
    </a>
</template>

<script lang="ts">
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
        url: {
            type: String,
            required: true,
        },
    },
};
</script>
