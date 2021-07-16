import Vue from "vue";

// Setup routes and authentication
import axios from "axios";
import VueRouter, { Route, RouteRecord } from 'vue-router';

import router from "./store/router";
import store from "./store/index";

axios.interceptors.request.use(function(config) {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

router.beforeEach(async (to: Route, from: Route, next: any) => {
  const token = localStorage.getItem("token");
  if (token) {
    await store.dispatch("auth/getAuthUser");
  }

  if (to.matched.some((record: RouteRecord) => record.meta.requiresAdmin)) {
    if (!store.state.auth.isLoggedIn()) {
      return next({ name: "Login" });
    }
    let isAdmin = store.state.auth.hasRole("ADMIN");
    if (!isAdmin) {
      return next({ name: "Error" });
    } else {
      return next();
    }
  }

  if (
    to.matched.some(
      (record) => record.meta.requiresAuth && record.meta.unverified !== true
    )
  ) {
    if (!store.state.auth.isLoggedIn()) {
      return next({ name: "Login" });
    }
    if (!store.state.auth.authUser.isVerified) {
      return next({ name: "VerifyAccount" });
    }
    return next();
  }

  if (
    to.matched.some(
      (record) => record.meta.requiresAuth && record.meta.unverified === true
    )
  ) {
    if (!store.state.auth.isLoggedIn()) {
      return next({ name: "Login" });
    }
    return next();
  }

  if (to.matched.some((record) => record.meta.anonymousOnly)) {
    if (store.state.auth.isLoggedIn()) {
      return next({ name: "Home" });
    } else {
      return next();
    }
  }

  return next();
});

// Setup Vue Meta
import VueMeta from "vue-meta";

Vue.use(VueMeta, {
  // optional pluginOptions
  refreshOnceOnNavigation: true,
});

// Setup Buefy design
import Buefy from "buefy";

import { library } from "@fortawesome/fontawesome-svg-core";
import {
  faHome,
  faGift,
  faTimes,
  faCheck,
  faCheckCircle,
  faStop,
  faStopCircle,
  faPlay,
  faPlayCircle,
  faEllipsisH,
  faBan,
  faInfoCircle,
  faExclamationTriangle,
  faExclamationCircle,
  faArrowUp,
  faArrowDown,
  faAngleUp,
  faAngleRight,
  faAngleLeft,
  faAngleDown,
  faEye,
  faEyeSlash,
  faMinus,
  faPlus,
  faCaretDown,
  faCaretUp,
  faUser,
  faMagic,
  faEdit,
  faCopy,
  faFilter,
  faSignInAlt,
  faSignOutAlt,
  faUpload,
  faDownload,
  faCalendarAlt,
  faTimesCircle,
  faEnvelope,
  faCalendar,
  faClock,
  faUsers,
  faCogs,
  faImages,
  faTrash,
  faBook,
} from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

library.add(
  faHome,
  faGift,
  faTimes,
  faCheck,
  faCheckCircle,
  faStop,
  faStopCircle,
  faPlay,
  faPlayCircle,
  faEllipsisH,
  faBan,
  faInfoCircle,
  faExclamationTriangle,
  faExclamationCircle,
  faArrowUp,
  faArrowDown,
  faAngleUp,
  faAngleRight,
  faAngleLeft,
  faAngleDown,
  faEye,
  faEyeSlash,
  faMinus,
  faPlus,
  faCaretDown,
  faCaretUp,
  faUser,
  faMagic,
  faEdit,
  faCopy,
  faFilter,
  faSignInAlt,
  faSignOutAlt,
  faUpload,
  faDownload,
  faCalendarAlt,
  faTimesCircle,
  faEnvelope,
  faCalendar,
  faClock,
  faUsers,
  faCogs,
  faImages,
  faTrash,
  faBook,
);
Vue.component("vue-fontawesome", FontAwesomeIcon);

Vue.use(Buefy, {
  defaultIconComponent: "vue-fontawesome",
  defaultIconPack: "fas",
});

// Setup Vue app
import App from "./App.vue";
import { i18n } from "./plugins/i18n/i18n";

new Vue({
  el: "#app",
  components: { App },
  template: "<App/>",
  router,
  store,
  i18n,
});
