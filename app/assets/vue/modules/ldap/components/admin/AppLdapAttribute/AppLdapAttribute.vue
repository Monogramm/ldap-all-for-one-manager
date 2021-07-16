<!-- Component responsible for the creation of inputs-->
<template>
  <div>
    <template
      v-for="(row, index) in values"
    >
      <div
        :key="`divColumns:${index}`"
        class="columns mb-0 is-mobile"
      >
        <div
          v-if="type === 'image' || type === 'file'"
          :key="`divValueInput:${index}`"
          class="column is-three-fifths is-offset-one-fifth"
        >
          <!-- Display current image -->
          <figure
            v-if="type === 'image'"
            class="image"
          >
            <b-image
              :src="formattedValue(index)"
            />
          </figure>
          <div class="mt-4 columns mt-1 is-centered">
            <b-field>
              <b-upload
                drag-drop
                @input="droppedFile($event, index)"
              >
                <section class="section">
                  <div class="content has-text-centered">
                    <p>
                      <b-icon
                        icon="upload"
                        size="is-large"
                      />
                    </p>
                    <p>Drop your file here or click to upload</p>
                  </div>
                </section>
              </b-upload>
            </b-field>
          </div>
        </div>
        <div
          v-else
          :key="`divValueInput:${index}`"
          class="column is-three-fifths is-offset-one-fifth"
        >
          <!-- Input value attribute-->
          <b-input
            :key="`inputValue:${index}`"
            v-model="values[index]"
            :value="row"
            :type="type"
            :title="$t('ldap.entries.new.value.value-title')"
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
            :title="$t('ldap.entries.new.value.del-title')"
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
          :title="$t('ldap.entries.new.value.add-title')"
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
    },
    type : {
      type: String,
      default: () => 'text',
    }
  },
  data() {
    return {
      /**
       * The LDAP attribute values.
       */
      values: this.value as Array<String>,
    };
  },
  methods: {
    addValue() {
      this.values.push('');
    },
    removeValue(index: number) {
      this.values.splice(index, 1);
    },
    getLastItem() {
      return this.values.length-1;
    },
    async droppedFile(file: File, index: number) {
      // TODO Control that file.type matches attribute type
      console.dir(file);

      await this.toBase64(file,
        (reader: FileReader, result: string | ArrayBuffer) => {
          if(typeof(result)=="string") {
            if (result.startsWith('data:')) {
              result = result.substring(result.indexOf(',')+1);
            }
          }

          // Save base64 into attribute values
          this.$set(this.values, index, result);
        },
        (reader: FileReader, ev: ProgressEvent<FileReader>) => {
          // TODO Display error toast
          console.error('Error occurred on file upload!', ev);
        }
      );
    },
    toBase64(file: File, resolve: (reader: FileReader, result: string | ArrayBuffer) => any, reject: (reader: FileReader, ev: ProgressEvent<FileReader>) => any) {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      if (!!resolve) {
        reader.onload = () => resolve(reader, reader.result);
      }
      if (!!reject) {
        reader.onerror = (error) => reject(reader, error);
      }
    },
    formattedValue(index: number): string {
      let formattedValue = '';

      switch (this.type) {
      case 'image':
        formattedValue = this.values[index];
        if (! formattedValue.startsWith('data:')) {
          formattedValue = 'data:image/png;base64,' + formattedValue;
        }
        break;

      default:
        formattedValue = this.values[index];
        break;
      }

      return formattedValue;
    },
  }
};
</script>

<style lang="scss" scoped>
</style>
