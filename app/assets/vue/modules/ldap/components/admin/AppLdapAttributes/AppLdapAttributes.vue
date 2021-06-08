<template>
  <div>
    <template
      v-for="(row, index) in parentAttributes"
      class="container"
    >
      <div
        :key="`div:${index}`"
        class="box"
      >
        <!-- Button for retrieving input-->
        <div class="columns is-centered">
          <div class="column is-1">
            <b-button
              type="is-danger"
              icon-right="trash"
              @click="removeInput(index)"
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
          @click="addInput()"
        >
          {{ $t('ldap.entries.add-a-attribute') }}
        </b-button>
      </div>
    </div>
       
    <!-- <template
    v-for="(key, value) in attributes"
    :key="key"
    :value="value"
  >
    <app-ldap-attribute />
  </template> -->
  </div>
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
      parentAttributes: this.attributes,
      appLdapAttribute: []
    };
  },
  methods: {
    updateAttribute() {
      //TODO Find if that funtion is useful
    },
    addInput() {
      //this.parentAttributes[this.keyAttribute] = [''];
      this.$set(this.parentAttributes,this.keyAttribute,[''])
    },
    removeInput(index: string) {
      this.$delete(this.parentAttributes,index)
      //delete this.parentAttributes[index];
      //this.attributes.splice(index, 1);
    },
  }
};
</script>

<style lang="scss" scoped>
</style>