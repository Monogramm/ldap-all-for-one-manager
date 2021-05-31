<template>
  <div class="section">
    <template
      v-for="(row, index) in appLdapAttributeArray"
    >
      <!-- Button for retrieving input-->
      <b-button
        v-if="appLdapAttributeArray.length > 1"
        :key="`deletebutton:${index}`"
        @click="removeField(index)"
      >
        {{ $t('common.delete') }}
      </b-button>

      <!-- Call AppLdapAttribute Component-->
      <app-ldap-attribute
        v-if="appLdapAttributeArray[0] != null"
        :key="`componentldapattribute:${index}`"
        :key-ldap="keyAttribute"
        :value="attributeArray[index]"
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
import AppLdapAttribute from "../../admin/AppLdapAttribute/AppLdapAttribute.vue";

export default {
  name: "AppLdapAttributes",
  components: { AppLdapAttribute },
  props: {
    attributes: {
      type: Object,
    },
    isLoading: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      keyAttribute: '',
      attributeArray: [],
      appLdapAttributeArray: new Array,
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
      this.appLdapAttributeArray.push(this.rowNumber++);
      this.attributeArray.push(['']);
    },
    removeField(index: Number) {
      this.appLdapAttributeArray.splice(index, 1);
    },
  }
};
</script>

<style lang="scss" scoped>
</style>