<template>
    <div class="resource-finder">
        <div class="resource-finder__loader" v-if="fetchingTopics">
            Loading...
        </div>
        <div class="resource-finder__error" v-else-if="errorFetchingTopics">
            <p>An error occurred, please try again later.</p>
        </div>
        <view-router v-else></view-router>
    </div>
</template>

<script lang="ts">
import { state, actions } from "./store";
import ViewRouter from "./view-router.vue";

export default {
    mounted() {
        actions.fetchTopics();
    },
    components: {
        "view-router": ViewRouter,
    },
    computed: {
        fetchingTopics() {
            return state.fetchingTopics;
        },
        errorFetchingTopics() {
            return state.errorFetchingTopics;
        },
    },
};
</script>
