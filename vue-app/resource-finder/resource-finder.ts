import Vue from "vue";
import ResourceFinder from "./resource-finder.vue";

export default function initResourceFinder() {
    const el = document.getElementById("resource-finder-root") as HTMLElement;
    if (el) {
        new Vue({
            components: {
                "resource-finder": ResourceFinder,
            },
            el,
        });
    }
}
