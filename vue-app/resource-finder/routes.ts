import CategoriesList from "./categories-list.vue";
import SubcategoriesList from "./subcategories-list.vue";
import TermsList from "./terms-list.vue";

const routes = {
    "/": {
        name: "Categories",
        component: CategoriesList,
    },
    "/category/:cid": {
        name: "Subcategories",
        component: SubcategoriesList,
    },
    "/category/:cid/subcategory/:sid*": {
        name: "Terms",
        component: TermsList,
    },
};

export default routes;
