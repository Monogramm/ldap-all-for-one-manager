<!-- Component responsible for the creation of inputs-->
<template>
  <div class="mb-2">
    <template
      v-for="(row, index) in attributes"
    >
      <div
        :key="`divColumns:${index}`"
        class="columns mb-0"
      >
        <div
          :key="`divValueInput:${index}`"
          class="column is-three-fifths is-offset-one-fifth"
        >
          <!-- Input value attribute-->
          <b-input
            :key="`valueInput:${index}`"
            v-model="attributes[index]"
            :value="row"
            type="text"
            :placeholder="$t('ldap.entries.ldap-placeholder')"
          />
        </div>
        <div
          :key="`divDeleteButton:${index}`"
          class="column is-2 pl-0"
        >
          <!-- Section for the input create/delete-->
          <b-button
            :key="`valueInputs:${index}`"
            type="is-danger"
            icon-right="trash"
            @click="removeInput(index)"
          />
        </div>
      </div>
    </template>
    <div
      class="columns"
    >
      <div class="column is-three-fifths is-offset-one-fifth" />
      <div class="column is-2 pl-0">
        <b-button
          icon-right="plus"
          @click="addInput()"
        />
      </div>
    </div>
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