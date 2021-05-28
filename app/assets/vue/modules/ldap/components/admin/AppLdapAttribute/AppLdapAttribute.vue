<!-- Component responsible for the creation of inputs-->
<template>
  <div class="section">
    <!-- Name of the table-->
    <div class="columns is-justify-content-center">
      <h1 class="title">
        {{ keyldap }}
      </h1>
    </div>
    <template
      v-for="(row, index) in valueArray.value"
    >
      <div
        :key="`valueInput:${index}`"
        class="columns"
      >
        <!-- Input value entry-->
        <div class="column is-2">
          <h4 class="title is-4">
            Value
          </h4>
        </div>
        <div class="column is-7">
          <b-input
            v-model="valueArray.value[index]"
            type="text"
            placeholder="ldap attribute in json format"
          />
        </div>
        <div class="column is-3">
          <!-- Section for the input create/delete-->
          <div class="buttons">
            <b-button
              v-show="index > 0"
              type="is-danger"
              icon-right="trash"
              @click="removeField(index)"
            />
            <b-button
              v-show="lastItem() === index"
              type="is-danger"
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
    indexinput : {
      type: Number
    },
    keyldap : {
      type: String
    },
    isLoading: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      valueArray: {key:this.keyldap,value:['']},
      attributesArray: []
    };
  },
  methods: {
    updateParent(property: string, value: string) {
      this.$emit("updateParent", property, value);
    },
    addField() {
      this.valueArray.value.push('');
    },
    removeField(index: Number) {
      this.valueArray.value.splice(index, 1);
    },
    addEntry()
    {
      this.attributesArray = {key:this.keyldap,value:this.valueArray}
    },
    lastItem() {
    	return this.valueArray.value.length-1;
    }
  }
};
</script>

  <style
    lang="scss"
    scoped
  />
</template>