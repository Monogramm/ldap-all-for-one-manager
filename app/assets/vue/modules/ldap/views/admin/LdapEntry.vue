<template>
  <app-ldapEntry
    v-if="entry !== null"
    :ldap-entry="entry"
    :is-loading="isLoading"
    @submit="onSubmit"
  />
</template>

<script lang="ts">
import { mapGetters } from "vuex";
import { ILdapEntry, LdapEntry, LdapEntryDefault } from "../../interfaces/entry";

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
      entry: null as LdapEntry | null
    };
  },
  computed: {
    // TODO Add types to ldapEntry getters
    ...mapGetters("ldapEntry", ["isLoading", "item", "hasError", "error"]),
    isEdit() {
      return !!this.id;
    }
  },
  async created() {
    if (this.id) {
      await this.$store
        .dispatch("ldapEntry/get", this.id).then((result: ILdapEntry) => {
          this.entry = result;
          //TODO Find a better way to implement id
          this.entry.id = this.id;
        });
    } else {
      this.entry = LdapEntryDefault();
    }
  },
  methods: {
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
        return this.editLdapEntry(this.id, this.entry);
      }

      return this.createLdapEntry(this.entry);
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
