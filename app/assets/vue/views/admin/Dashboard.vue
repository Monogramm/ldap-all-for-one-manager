<template>
  <section class="section">
    <div class="content">
      <h1 class="title is-1">
        {{ titleLabel }}
      </h1>
    </div>

    <section class="section box buttons modules">
      <b-button
        v-for="route in routes" 
        :key="route.path"
        tag="router-link"
        :to="route.path"
        :icon-left="route.meta.adminDashboard.icon"
      >
        {{ $t(route.meta.adminDashboard.label) }}
      </b-button>
    </section>
  </section>
</template>

<script lang="ts">
import router from "../../store/router";

export default {
  name: "AdminDashboard",
  data(): any {
    return {
      // Automatically retrieve routes for admin dashboard
      routes: router.getRoutes().filter(route => route.meta.adminDashboard),
    };
  },
  computed: {
    titleLabel() {
      return this.$t("admin.dashboard");
    }
  },
  metaInfo() {
    return { title: this.titleLabel };
  }
};
</script>
