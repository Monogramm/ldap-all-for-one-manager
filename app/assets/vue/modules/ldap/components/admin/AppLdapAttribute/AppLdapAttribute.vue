<!-- Component responsible for the creation of inputs-->
<template>
  <div class="">
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
            :key="`inputValue:${index}`"
            v-model="attributes[index]"
            :value="row"
            type="text"
            :placeholder="$t('ldap.entries.new.value.value-placeholder')"
          />
        </div>
        <div
          :key="`divDeleteButton:${index}`"
          class="column is-2 pl-0"
        >
          <!-- Section for the input create/delete-->
          <b-button
            :key="`buttonRemoveValue:${index}`"
            type="is-danger"
            icon-right="trash"
            @click="removeValue(index)"
          />
        </div>
      </div>
    </template>
    <div
      class="columns mb-0 is-mobile"
    >
      <div class="column is-three-fifths is-offset-one-fifth" />
      <div class="column is-2 pl-0">
        <b-button
          icon-right="plus"
          @click="addValue()"
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
    addValue() {
      this.attributes.push('');
    },
    removeValue(index: number) {
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
