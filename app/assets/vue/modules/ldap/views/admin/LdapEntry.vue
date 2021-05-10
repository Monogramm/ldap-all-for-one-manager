<template>
  <app-ldapEntry
    :ldap-entry="item"
    :types="types"
    :is-loading="isLoading"
    @updateParent="onChildPropsChanged"
    @submit="onSubmit"
  />
</template>

<script lang="ts">
import { mapGetters } from "vuex";
import { ILdapEntry } from "../../interfaces/entry";

import AppLdapEntry from "../../components/admin/AppLdapEntry/AppLdapEntry.vue";

export default {
  name: "LdapEntry",
  components: { AppLdapEntry },
  props: {
    id: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      types: [] as string[]
    };
  },
  computed: {
    // TODO Add types to ldapEntry getters
    ...mapGetters("ldapEntry", ["isLoading", "item", "hasError", "error"]),
    isEdit() {
      return !!this.id;
    }
  },
  created() {
    if (this.id) {
      this.$store
        .dispatch("ldapEntry/get", this.id);
    }
  },
  methods: {
    onChildPropsChanged(property: string, value: string) {
      this.item[property] = value;
    },
    async editLdapEntry(id: string, ldapEntry: ILdapEntry) {
      await this.$store
        .dispatch("ldapEntry/update", ldapEntry)
        .then(() => {
          if (!this.hasError) {
            this.$router.replace({ name: "AdminLdapEntries" });
          }
        });
    },
    async createLdapEntry(ldapEntry: ILdapEntry) {
      await this.$store
        .dispatch("ldapEntry/create", ldapEntry)
        .then(() => {
          if (!this.hasError) {
            this.$router.replace({ name: "AdminLdapEntries" });
          }
        });
    },
    onSubmit() {
      if (this.isEdit) {
        return this.editLdapEntry(this.id, this.item);
      }

      return this.createLdapEntry(this.item);
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
