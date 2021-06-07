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
      v-for="(row, index) in attributes"
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
        <div class="column is-7 mb-0">
          <b-input
            v-model="attributes[index]"
            :value="row"
            type="text"
            :placeholder="$t('ldap.entries.ldap-placeholder')"
          />
        </div>
        <div class="column is-3">
          <!-- Section for the input create/delete-->
          <div class="buttons">
            <b-button
              type="is-danger"
              icon-right="trash"
              @click="removeInput(index)"
            />
            <b-button
              v-if="getLastItem() === index"
              icon-right="plus"
              @click="addInput()"
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
    keyAttribut : {
      type: String,
      default: '',
    },
    value : {
      type: Array,
      default: () => Array<String>(),
    }
  },
  data() {
    return {
      attributes: this.value,
      attributeKey: this.keyAttribut,
    };
  },
  methods: {
    addInput() {
      this.attributes.push('');
    },
    removeInput(index: number) {
      this.attributes.splice(index, 1);
    },
    getLastItem() {
    	return this.attributes.length-1;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>