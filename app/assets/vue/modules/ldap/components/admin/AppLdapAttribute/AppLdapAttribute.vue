<!-- Component responsible for the creation of inputs-->
<template>
  <div class="section">
    <!-- Name of the table-->
    <div class="columns is-justify-content-center">
      <h1 class="title">
        {{ attributeKey }}
      </h1>
    </div>
    <template
      v-for="(row, index) in attributeValues"
    >
      <div
        :key="`valueInput:${index}`"
        class="columns"
      >
        <!-- Input value attribute-->
        <div class="column is-2">
          <h4 class="title is-4">
            {{ $t('ldap.entries.value') }}
          </h4>
        </div>
        <div class="column is-7">
          <b-input
            v-model="attributeValues[index]"
            type="text"
            placeholder="ldap attribute"
          />
        </div>
        <div class="column is-3">
          <!-- Section for the input create/delete-->
          <div class="buttons">
            <b-button
              type="is-danger"
              icon-right="trash"
              @click="removeField(index)"
            />
            <b-button
              v-if="lastItem() === index"
              icon-right="plus"
              @click="addField()"
            />
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script lang="ts">
export default {
  name: "AppLdapAttribute",
  props: {
    keyLdap : {
      type: String,
      default: '',
    },
    value : {
      type: Array,
      default: [''],
    },
    isLoading: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      attribute: {keyLdap:this.keyLdap,value:[]},
      attributeKey: this.keyLdap,
      attributeValues: this.value
    };
  },
  methods: {
    updateParent(property: string, value: Array<String>) {
      this.$emit("", property, value);
    },
    addField() {
      this.attributeValues.push('');
    },
    removeField(index: Number) {
      this.attributeValues.splice(index, 1);
    },
    lastItem() {
    	return this.attributeValues.length-1;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>