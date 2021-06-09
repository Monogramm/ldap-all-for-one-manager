import { RouteConfig } from "@/vue/interfaces/router";

import AdminLdapEntries from "./views/admin/LdapEntries.vue";
import AdminLdapEntry from "./views/admin/LdapEntry.vue";
//const AdminLdapEntries = () => import(/* webpackChunkName: "AdminLdapEntries" */ "./views/admin/LdapEntries.vue").then((m: any) => m.default);
//const AdminLdapEntry = () => import(/* webpackChunkName: "AdminLdapEntry" */ "./views/admin/LdapEntry.vue").then((m: any) => m.default);

export const LdapEntryRoutes: RouteConfig[] = [
  {
    name: "AdminLdapEntries",
    path: "/admin/ldap-entries",
    component: AdminLdapEntries,
    meta: {
      requiresAuth: true,
      requiresAdmin: true,
      adminDashboard: {
        label: "ldap.entries.admin",
        icon: "users",
      },
    },
  },
  {
    name: "LdapEntryEdit",
    path: "/admin/ldap-entry/:id",
    component: AdminLdapEntry,
    meta: {
      requiresAuth: true,
      requiresAdmin: true,
    },
    props: (route) => ({ id: route.params.id }),
  },
  {
    name: "LdapEntryCreate",
    path: "/admin/ldap-entry",
    component: AdminLdapEntry,
    meta: {
      requiresAuth: true,
      requiresAdmin: true,
    },
  },
];
