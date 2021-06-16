<template>
  <app-ldap-entries
    :is-loading="isLoading"
    :ldap-entries="items"
    :per-page="pagination.size"
    :total="total"
    @create="onCreate"
    @edit="onEdit"
    @delete="onDelete"
    @pageChanged="onPageChange"
    @filtersChanged="onFiltersChange"
    @sortingChanged="onSortingChange"
  />
</template>

<script lang="ts">
import { mapGetters } from "vuex";

import { Pagination } from "../../../../interfaces/pagination";
import { Criteria } from "../../../../interfaces/criteria";
import { Sort } from "../../../../interfaces/sort";

import AppLdapEntries from "../../components/admin/AppLdapEntries/AppLdapEntries.vue";

export default {
  name: "LdapEntries",
  components: { AppLdapEntries },
  data() {
    return {
      pagination: new Pagination(0, 0),
    };
  },
  computed: {
    ...mapGetters("ldapEntry", ["items", "isLoading", "total"])
  },
  created() {
    this.load();
  },
  methods: {
    load() {
      this.$store.dispatch("ldapEntry/getAll", this.pagination);
    },
    onPageChange(page: string) {
      this.pagination.page = page;
      if (this.pagination.size > 0) {
        this.load();
      }
    },
    onFiltersChange(filters: any) {
      this.pagination.criteria = new Criteria(filters);
      if (this.pagination.size > 0) {
        this.load();
      }
    },
    onSortingChange(field: string, order: string) {
      this.pagination.orderBy = new Sort(field, order);
      if (this.pagination.size > 0) {
        this.load();
      }
    },
    onEdit(paramId: string) {
      this.$router.push({ name: "LdapEntryEdit", params: { id: paramId } });
    },
    onCreate() {
      this.$router.push({ name: "LdapEntryCreate" });
    },
    onDelete(id: string) {
      this.$buefy.dialog.confirm({
        title: this.$t("common.confirmation.delete"),
        message: this.$t("common.confirmation.delete-message"),
        cancelText: this.$t("common.cancel"),
        confirmText: this.$t("common.delete"),
        type: "is-danger",
        hasIcon: true,
        onConfirm: () => {
          this.$store.dispatch("ldapEntry/delete", id);
          this.$buefy.toast.open(this.$t("common.confirmation.deleted"));
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
