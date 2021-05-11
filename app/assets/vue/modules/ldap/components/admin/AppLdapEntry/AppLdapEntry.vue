<template>
  <div class="section">
    <h1 class="title is-1">
      {{ $t(isEdit ? "ldap.entries.edit" : "ldap.entries.create") }}
    </h1>

    <form
      class="box"
      @submit.prevent
    >
      <b-field :label="$t('ldap.entries.dn')">
        <b-input
          v-model="ldapEntry.dn"
          maxlength="254"
          required
          :disabled="isLoading"
          @change="updateParent('dn', $event.target.value)"
        />
      </b-field>


      <!--
      <b-field :label="$t('ldap.entries.attribute')">
        <b-input
          v-model="ldapEntry.attributes"
          maxlength="254"
          required
          :disabled="isLoading"
          @change="updateParent('name', $event.target.value)"
        />
      </b-field>

      <b-field :label="$t('ldap.entries.value')">
        <b-input
          v-model="ldapEntry.value"
          maxlength="254"
          required
          :disabled="isLoading"
          @change="updateParent('value', $event.target.value)"
        />
      </b-field>
      -->

      <b-button
        type="is-primary"
        native-type="submit"
        :loading="isLoading"
        @click="submit"
      >
        {{ $t(isEdit ? 'common.edit' : 'common.create') }}
      </b-button>
    </form>
  </div>
</template>

<script lang="ts">
import { ILdapEntry, LdapEntry } from "../../../interfaces/entry";

export default {
  name: "AppLdapEntry",
  props: {
    ldapEntry: {
      type: Object,
      default: () => new LdapEntry(null)
    },
    isLoading: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    isEdit() {
      return !!this.ldapEntry.dn;
    }
  },
  methods: {
    updateParent(property: string, value: string) {
      this.$emit("updateParent", property, value);
    },
    submit() {
      this.$emit("submit");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
