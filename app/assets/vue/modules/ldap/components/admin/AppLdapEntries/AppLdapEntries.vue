<template>
  <b-table
    :data="ldapEntries"
    :loading="isLoading"
    :aria-next-label="nextPageLabel"
    :aria-previous-label="previousPageLabel"
    :aria-page-label="pageLabel"
    :aria-current-label="currentPageLabel"
    :paginated="perPage > 0"
    backend-pagination
    :total="total"
    :per-page="perPage"
    :backend-filtering="perPage > 0"
    :debounce-search="500"
    :backend-sorting="perPage > 0"
    @page-change="onPageChange"
    @filters-change="onFiltersChange"
    @sort="onSortingChange"
  >
    <b-table-column
      v-slot="props"
      field="dn"
      searchable
      sortable
      :label="dnColumnLabel"
    >
      {{ props.row.dn }}
    </b-table-column>

    <b-table-column
      v-slot="props"
      field="buttons"
    >
      <div class="buttons">
        <b-button
          type="is-warning"
          icon-left="edit"
          :title="$t('ldap.entries.edit')"
          @click="edit(props.row.dn)"
        >
          {{ $t("common.edit") }}
        </b-button>
        <b-button
          type="is-warning"
          icon-left="copy"
          :title="$t('ldap.entries.clone')"
          @click="clone(props.row.dn)"
        >
          {{ $t("common.clone") }}
        </b-button>
        <b-button
          type="is-danger"
          icon-left="trash"
          :title="$t('ldap.entries.delete')"
          @click="deleteLdapEntry(props.row.dn)"
        >
          {{ $t("common.delete") }}
        </b-button>
      </div>
    </b-table-column>
  </b-table>
</template>

<script lang="ts">
import { truncate } from "../../../../../common/helpers";

export default {
  name: "AppLdapEntries",
  filters: {
    shorten(value: string | null, length: number) {
      return truncate(value, length);
    },
  },
  props: {
    ldapEntries: {
      type: Array,
      default: (): [] => [],
    },
    total: {
      type: Number,
      default: 0,
    },
    perPage: {
      type: Number,
      default: 50,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    dnColumnLabel() {
      return this.$t("ldap.entries.dn");
    },
    nextPageLabel() {
      return this.$t("table.next-page");
    },
    previousPageLabel() {
      return this.$t("table.previous-page");
    },
    pageLabel() {
      return this.$t("table.page");
    },
    currentPageLabel() {
      return this.$t("table.current-page");
    },
  },
  methods: {
    edit(id: string) {
      this.$emit("edit", id);
    },
    clone(id: string) {
      this.$emit("clone", id);
    },
    deleteLdapEntry(id: string) {
      this.$emit("delete", id);
    },
    onPageChange(page: number) {
      this.$emit("pageChanged", page);
    },
    onFiltersChange(filters: any) {
      this.$emit("filtersChanged", filters);
    },
    onSortingChange(field: string, order: string) {
      this.$emit("sortingChanged", field, order);
    }
  },
};
</script>

<style lang="scss" scoped></style>
