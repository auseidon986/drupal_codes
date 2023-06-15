import Vue from "vue";
import axios from "axios";
import { getAPIBaseByEnvironment } from "./util";

export const state = Vue.observable({
    routeContext: null,
    currentRoute: null,
    selectedCategoryId: null,
    selectedSubcategoryId: null,
    parentSubcategoryIds: [],
    fetchingTopics: true,
    errorFetchingTopics: false,
    topics: [],
});

export const props = {
    topicsByIds: [],
    topicsByParents: [],
};

export const actions = {
    fetchTopics() {
        state.fetchingTopics = true;
        state.errorFetchingTopics = false;

        axios
            .get(getAPIBaseByEnvironment())
            .then((response) => {
                state.topics = response.data;
                state.fetchingTopics = false;
                props.topicsByParents = getters.generateTopicIdsByParentId();
                props.topicsByIds = getters.generateTopicsById();
            })
            .catch((error) => {
                console.log(error);
                state.fetchingTopics = false;
                state.errorFetchingTopics = true;
            });
    },
};

export const getters = {
    generateTopicsById() {
        return state.topics.reduce(
            (acc, cur) => ({
                ...acc,
                [cur.id]: cur,
            }),
            {}
        );
    },
    topicsById() {
        return props.topicsByIds;
    },
    hasChildren(id) {
        return !!props.topicsByParents[id];
    },
    generateTopicIdsByParentId() {
        return state.topics.reduce((acc, cur) => {
            const parentId = cur.parent_id[0] as string;
            if (!acc.hasOwnProperty(parentId)) acc[parentId] = [];
            acc[parentId].push(cur.id);
            return acc;
        }, {});
    },
    topicIdsByParentId() {
        return props.topicsByParents;
    },
};
