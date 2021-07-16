<template>
  <div class="section">
    <h1 class="title is-1">
      {{ $t(isEdit ? "ldap.entries.edit" : "ldap.entries.create") }}
    </h1>

    <form
      role="form"
      class="box"
      @submit.prevent
    >
      <div class="columns mt-1 is-centered">
        <div class="column is-10">
          <h1
            v-if="isEdit"
            class="title is-3 is-size-4-mobile"
          >
            {{ newEntry.dn }}
          </h1>
          <div v-else>
            <!-- Default DN input-->
            <b-field
              :label="$t('ldap.entries.dn')"
            >
              <b-input
                v-model="newEntry.dn"
                maxlength="254"
                required
                :title="$t('ldap.entries.new.dn.title-input')"
                :placeholder="$t('ldap.entries.new.dn.key-placeholder')"
                :disabled="isLoading"
              />
            </b-field>
          </div>
        </div>
      </div>
      <!-- Call the AppLdapAttributes Component-->
      <app-ldap-attributes
        :values="newEntry.attributes"
      />
      <div class="columns is-centered is-mobile mt-5">
        <!-- Button submit call onSubmit() from parent-->
        <b-button
          :disabled="ldapEntry.dn == '' || ldapEntry.dn == null "
          :loading="isLoading"
          :title="$t('ldap.entries.new.save')"
          type="is-primary"
          native-type="submit"
          @click="submit"
        >
          {{ $t(isEdit ? 'common.update' : 'common.create') }}
        </b-button>
      </div>
    </form>
  </div>
</template>

<script lang="ts">
import { LdapEntryDefault } from "../../../interfaces/entry";
import AppLdapAttributes from "../../admin/AppLdapAttributes/AppLdapAttributes.vue";

export default {
  name: "AppLdapEntry",
  components: { AppLdapAttributes },
  props: {
    ldapEntry: {
      type: Object,
      default: LdapEntryDefault
    },
    isLoading: {
      type: Boolean,
      default: false
    },
    isEdit: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      newEntry: this.ldapEntry,
    };
  },
  methods: {
    submit() {
      this.$emit("submit");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
