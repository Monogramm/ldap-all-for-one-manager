<template>
  <div class="section">
    <h1 class="title is-1">
      {{ $t(isEdit ? "ldap.entries.edit" : "ldap.entries.create") }}
    </h1>

    <form
      class="box"
      @submit.prevent
    >
      <!-- Default DN input-->
      <b-field :label="$t('ldap.entries.dn')">
        <b-input
          v-model="ldapEntry.dn"
          maxlength="254"
          required
          :disabled="isLoading"
        />
      </b-field>
      <!-- Call the AppLdapAttributes Component-->
      <app-ldap-attributes
        :attributes="ldapEntry.attributes"
        @updateAttribute="updateAttribute"
      />

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
import AppLdapAttributes from "../../admin/AppLdapAttributes/AppLdapAttributes.vue";

export default {
  name: "AppLdapEntry",
  components: { AppLdapAttributes },
  props: {
    ldapEntry: {
      type: Object,
      default: () => new LdapEntry(null,null)
    },
    isLoading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      //TODO Find why this.ldapEntry made reference to the default object
      newEntry: this.ldapEntry,
    };
  },
  computed: {
    isEdit() {
      return !!this.ldapEntry.dn;
    }
  },
  methods: {
    updateAttribute(attribute: Array<String>) {
      console.log("Update")
      this.newEntry.attributes = attribute;
    },
    submit() {
      this.newEntry.dn = "cn=Sika Dore,ou=people,dc=planetexpress,dc=com"
      this.newEntry.attributes = {sn:["Jojo"],objectClass:["inetOrgPerson"]}
      this.$emit("submit");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
