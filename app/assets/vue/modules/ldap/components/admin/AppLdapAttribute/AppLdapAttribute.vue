<!-- Component responsible for the creation of inputs-->
<template>
  <div class="mb-2">
    <template
      v-for="(row, index) in attributes"
    >
      <div
        :key="`divColumns:${index}`"
        class="columns mb-0 is-mobile"
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
            :key="`valueRemoveInput:${index}`"
            type="is-danger"
            icon-right="trash"
            @click="removeInput(index)"
          />
        </div>
      </div>
    </template>
    <div
      class="columns is-mobile"
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
    value : {
      type: Array,
      default: () => Array<String>(),
    }
  },
  data() {
    return {
      attributes: this.value,
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