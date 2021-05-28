<template>
  <div class="section">
    <template
      v-for="(row, index) in attributeArray"
    >
      <!-- Button for retrieving input-->
      <b-button
        v-show="attributeArray.length > 1"
        :key="`deletebutton:${index}`"
        @click="removeField(index)"
      >
        {{ $t('common.delete') }}
      </b-button>

      <!-- Call AppLdapAttribute Component-->
      <app-ldap-attribute
        v-show="attributeArray != null"
        :key="`componentldapattribute:${index}`"
        :indexinput="index"
        :keyldap="row"
      />
    </template>

    <div class="columns">
      <!-- Input value key-->
      <div class="column is-one-third is-two-fifths is-offset-one-quarter">
        <b-field
          label="key" 
        >
          <b-input
            v-model="keyEntry"
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
import AppLdapAttribute from "../../admin/AppLdapAttribute/AppLdapAttribute.vue";

export default {
  name: "AppLdapAttributes",
  components: { AppLdapAttribute },
  props: {
    attributes: {
      type: Object,
      default: () => Array<any>(),
    },
    isLoading: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      keyEntry: '',
      attributeArray: new Array,
      rowNumber: 0
    };
  },
  computed: {
    isEdit() {
      return !!this.ldapEntry.dn;
    }
  },
  methods: {
    addField() {
      this.attributeArray.push(this.keyEntry);
    },
    removeField(index: Number) {
      this.attributeArray.splice(index, 1);
    },
  }
};
</script>

<style lang="scss" scoped>
</style>