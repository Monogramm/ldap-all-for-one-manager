import Vue from "vue";
import VueRouter from "vue-router";

import Index from "../views/Index.vue";
import Home from "../views/Home.vue";

import ToS from "../views/ToS.vue";
import Privacy from "../views/Privacy.vue";
import Install from "../views/Install.vue";

import NotFound from "../views/NotFound/NotFound.vue";
import Error from "../views/Error/Error.vue";

import AdminDashboard from "../views/admin/Dashboard.vue";
//const AdminDashboard = () => import(/* webpackChunkName: "AdminUsers" */ "../views/admin/Dashboard.vue").then((m: any) => m.default);

import { AuthRoutes } from "../modules/auth/routes";
import { UserRoutes } from "../modules/user/routes";
import { SupportRoutes } from "../modules/support/routes";
import { ParameterRoutes } from "../modules/parameter/routes";
import { BackgroundJobRoutes } from "../modules/backgroundJob/routes";
import { CurrencyRoutes } from "../modules/currency/routes";
import { MediaRoutes } from "../modules/media/routes";
import { LdapEntryRoutes } from "../modules/ldap/routes";

Vue.use(VueRouter);

export const router: VueRouter = new VueRouter({
  mode: "history",
  routes: [
    // catch-all route
    // shows 404 page and also makes server respond with HTTP status code 404
    {
      path: "*",
      component: NotFound,
    },
    {
      name: "NotFound",
      path: "/not-found",
      component: NotFound,
    },
    {
      name: "Error",
      path: "/error",
      component: Error,
    },
    {
      name: "Index",
      path: "/",
      component: Index,
    },
    {
      name: "Home",
      path: "/home",
      component: Home,
      meta: {
        requiresAuth: true,
        unverified: true,
      },
    },
    {
      name: "ToS",
      path: "/terms-of-services",
      component: ToS,
    },
    {
      name: "Privacy",
      path: "/privacy-policy",
      component: Privacy,
    },
    {
      name: "Install",
      path: "/install",
      component: Install,
    },
    {
      name: "AdminDashboard",
      path: "/admin",
      component: AdminDashboard,
      meta: {
        requiresAuth: true,
        requiresAdmin: true,
      },
    },
    ...AuthRoutes,
    ...UserRoutes,
    ...SupportRoutes,
    ...ParameterRoutes,
    ...BackgroundJobRoutes,
    ...CurrencyRoutes,
    ...MediaRoutes,
    ...LdapEntryRoutes,
  ],
});

export default router;
