<template>
  <div>
    <template
      v-for="(row, index) in attributes"
      class="container"
    >
      <div
        :key="`div:${index}`"
        class="box"
      >
        <!-- Button for retrieving input-->
        <div class="columns mt-2">
          <!-- Name of the table-->
          <div class="ml-6 is-flex">
            <h1 class="title has-text-centered">
              {{ index }}
            </h1>
          </div>
          <div class="column is-1">
            <b-button
              type="is-danger"
              icon-right="trash"
              @click="removeAttribute(index)"
            />
          </div>
        </div>
        <!-- Call AppLdapAttribute Component-->
        <app-ldap-attribute
          :key="`componentldapattribute:${index}`"
          :key-attribut="index"
          :value="row"
        />
      </div>
    </template>

    <div class="columns">
      <div class="column is-one-third is-two-fifths is-offset-one-quarter">
        <b-field
          :label="$t('ldap.entries.key')" 
        >
          <b-input
            v-model="newAttributeKey"
            required
            :placeholder="$t('ldap.entries.example-input')"
            type="text"
          />
        </b-field>

        <b-button
          @click="addAttribute()"
        >
          {{ $t('ldap.entries.add-a-attribute') }}
        </b-button>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { PropType } from 'vue';

import { ILdapAttributes, ILdapEntry, LdapEntry } from "../../../interfaces/entry";

import AppLdapAttribute from "../../admin/AppLdapAttribute/AppLdapAttribute.vue";

export default {
  name: "AppLdapAttributes",
  components: { AppLdapAttribute },
  props: {
    values : {
      type: Object as PropType<ILdapAttributes>,
      default: {},
    }
  },
  data() {
    return {
      newAttributeKey: '',
      attributes: this.values,
    };
  },
  methods: {
    addAttribute() {
      // XXX Remove default empty string as first value when LDAP schema config available.
      this.$set(this.attributes, this.newAttributeKey, ['']);
    },
    removeAttribute(index: string) {
      this.$delete(this.attributes, index);
    },
  }
};
</script>

<style lang="scss" scoped>
</style>