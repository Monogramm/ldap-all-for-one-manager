<template>
  <div class="section">
    <h1 class="title is-1">
      {{ $t(isEdit ? "ldap.entries.edit" : "ldap.entries.create") }}
    </h1>

    <form
      class="box"
      @submit.prevent
    >
      <div class="columns mt-1 is-centered">
        <div class="column is-10">
          <h1
            v-if="isEdit"
            class="title is-3"
          >
            {{ newEntry.dn }}
          </h1>
          <div v-else>
            <!-- Default DN input-->
            <b-field :label="$t('ldap.entries.dn')">
              <b-input
              
                v-model="newEntry.dn"
                maxlength="254"
                required
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
      <!-- Button submit call onSubmit() from parent-->
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
import { ILdapEntry, LdapEntry, LdapEntryDefault } from "../../../interfaces/entry";
import AppLdapAttributes from "../../admin/AppLdapAttributes/AppLdapAttributes.vue";

export default {
  name: "AppLdapEntry",
  components: { AppLdapAttributes },
  props: {
    ldapEntry: {
      type: Object,
      default: LdapEntryDefault
    },
    id: {
      type: String,
      default: ''
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
      return !!this.id;
    }
  },
  methods: {
    updateAttribute(attribute: Object) {
      //TODO Find if that funtion is useful
    },
    submit() {
      this.$emit("submit");
    }
  }
};
</script>

<style lang="scss" scoped>
</style>
