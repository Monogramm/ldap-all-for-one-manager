<template>
  <div class="section">
    <template
      v-for="(row, index) in attributes"
    >
      <!-- Button for retrieving input-->
      <b-button
        :key="`deletebutton:${index}`"
        @click="removeField(index)"
      >
        {{ $t('common.delete') }}
      </b-button>
      <!-- " -->
      <!-- Call AppLdapAttribute Component-->
      <app-ldap-attribute
        :key="`componentldapattribute:${index}`"
        :key-attribut="index"
        :value="row"
      />
    </template>

    <div class="columns">
      <div class="column is-one-third is-two-fifths is-offset-one-quarter">
        <b-field
          label="key" 
        >
          <!-- Input value keyLdap-->
          <b-input
            v-model="keyAttribute"
            required
            placeholder="key for ldap attribute"
            type="text"
          />
        </b-field>
        <b-button
          @click="addField()"
        >
          {{ $t('ldap.entries.add-a-attribute') }}
        </b-button>
      </div>
    </div>
  </div>
       
  <!-- <template
    v-for="(key, value) in attributes"
    :key="key"
    :value="value"
  >
    <app-ldap-attribute />
  </template> -->
</template>

<script lang="ts">
import { ILdapAttributes, ILdapEntry, LdapEntry } from "../../../interfaces/entry";
import AppLdapAttribute from "../../admin/AppLdapAttribute/AppLdapAttribute.vue";
import { PropType } from 'vue';

export default {
  name: "AppLdapAttributes",
  components: { AppLdapAttribute },
  props: {
    attributes : {
      type: Object as PropType<ILdapAttributes>,
      default: {},
    }
  },
  data() {
    return {
      keyAttribute: '',
      //TODO Find why this.attributes made reference to the default object
      appLdapAttributes: this.attributes,
    };
  },
  computed: {
    isEdit() {
      return !!this.ldapEntry.dn;
    },
  },
  methods: {
    updateAttribute() {
      // this.$emit("updateAttribute","");
    },
    addField() {
      this.attributes[this.keyAttribute] = [''];
    },
    removeField(index: string) {
      //TODO Find a way 
      delete this.attributes[index];
      //this.attributes.splice(index, 1);
    },
  }
};
</script>

<style lang="scss" scoped>
</style>