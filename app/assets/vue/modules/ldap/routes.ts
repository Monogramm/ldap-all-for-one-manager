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
        icon: "book",
      },
    },
  },
  {
    name: "LdapEntryEdit",
    path: "/admin/ldap-entry/:dn",
    component: AdminLdapEntry,
    meta: {
      requiresAuth: true,
      requiresAdmin: true,
    },
    props: (route) => ({ dn: route.params.dn }),
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
