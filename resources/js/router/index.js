import { createRouter, createWebHistory } from "vue-router";
import Login from "../views/Login.vue";
import Companies from "../views/Companies.vue";
import CompanyDetail from "../views/CompanyDetail.vue";

const routes = [
    { path: "/", component: Login },
    { path: "/companies", component: Companies },
    { path: "/companies/:id", component: CompanyDetail },
];

export default createRouter({
    history: createWebHistory(),
    routes,
});
