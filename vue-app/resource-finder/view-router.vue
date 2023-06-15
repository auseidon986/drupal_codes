<template>
    <div class="resource-finder__router">
        <transition
            name="view-fade"
            mode="out-in"
            appear
            appear-class="view-fade-appear"
            appear-to-class="view-fade-appear-to"
            appear-active-class="view-fade-appear-active"
            @beforeLeave="onBeforeLeave"
            @appear="
                () => {
                    /* Prevent enter handler from firing on initial mount using noop */
                }
            "
            @enter="onEnter"
            @afterEnter="onAfterEnter"
        >
            <div :key="currentPath">
                <component :is="currentView"></component>
            </div>
        </transition>
    </div>
</template>
<script lang="ts">
import { state } from "./store";
import { getRouteBaseByEnvironment } from "./util";
import routes from "./routes";
import page from "page";

export default {
    beforeCreate() {
        page.base(getRouteBaseByEnvironment());

        Object.keys(routes).forEach((route) => {
            page(route, (ctx) => {
                state.routeContext = ctx;
                state.currentRoute = routes[route];
                state.selectedCategoryId = ctx.params.cid || null;

                let subIds = ctx.params.sid;
                if (!!subIds) {
                    subIds = subIds.split('/');
                    state.selectedSubcategoryId = subIds.pop() || null;
                    state.parentSubcategoryIds = subIds || [];
                } else {
                    state.selectedSubcategoryId = null;
                    state.parentSubcategoryIds = [];
                }
            });
        });

        page({
            hashbang: true,
        });
    },
    data: () => ({
        previousHeight: 0,
    }),
    computed: {
        currentView() {
            if (!!state.currentRoute) {
                return state.currentRoute.component;
            } else {
                return null;
            }
        },
        currentPath() {
            return state.routeContext.path;
        }
    },
    methods: {
        onBeforeLeave(element: HTMLElement) {
            // console.log('onBeforeLeave');
            this.previousHeight = getComputedStyle(element).height;
        },
        onEnter(element: HTMLElement) {
            // console.log('onEnter');
            const wrapper = document.querySelector(".resource-finder__list-inner") as HTMLElement;
            const newHeight = getComputedStyle(element).height;
            element.style.height = this.previousHeight;
            element.addEventListener("transitionend", (event) => {
                if (event.propertyName === "height") {
                    element.style.height = null;
                }
            });
            setTimeout(() => {
                element.style.height = newHeight;
                if (!!wrapper && window.pageYOffset > wrapper.getBoundingClientRect().top + window.pageYOffset) {
                    window.scrollTo({
                        top: this.$el.getBoundingClientRect().top + window.pageYOffset,
                        left: 0,
                        behavior: "smooth",
                    });
                }
            }, 0);
        },
        onAfterEnter(element: HTMLElement) {
            // console.log('onAfterEnter');
            element.style.height = null;
        },
    },
};
</script>
