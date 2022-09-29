<template>
  <modal :show="show" @close="close">
    <div v-if="field">
      <div class="flex">
        <h2 class="text-2xl font-bold z-10 truncate mb-5 text-nt-blue flex-grow">
          Configure "<span class="truncate">{{ field.name }}</span>" block
        </h2>
        <div>
          <v-button color="red" size="small" @click="removeBlock">
            Remove
          </v-button>
          <v-button  size="small" @click="duplicateBlock">
            Duplicate
          </v-button>
        </div>
      </div>
      <div v-if="field.type == 'nf-text'" class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <rich-text-area-input name="content"
                              :form="field"
                              label="Content"
                              :required="false"
        />
      </div>
      <div v-else-if="field.type == 'nf-page-break'" class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <text-input name="next_btn_text"
                    :form="field"
                    label="Text of next button"
                    :required="true"
        />
        <text-input name="previous_btn_text"
                    :form="field"
                    label="Text of previous button"
                    help="Shown on the next page"
                    :required="true"
        />
      </div>
      <div v-else-if="field.type == 'nf-page-body-input'" class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <div class="-mx-4 sm:-mx-6 p-5 pt-0 border-b">
          <h3 class="font-semibold block text-lg">
            General
          </h3>
          <p class="text-gray-400 mb-5">
            Exclude this field or make it required.
          </p>
          <v-checkbox v-model="field.hidden" class="mb-3"
                      :name="field.id+'_hidden'"
                      @input="onFieldHiddenChange"
          >
            Hidden
          </v-checkbox>
          <v-checkbox v-model="field.required"
                      :name="field.id+'_required'"
                      @input="onFieldRequiredChange"
          >
            Required
          </v-checkbox>
        </div>
        <div class="-mx-4 sm:-mx-6 p-5">
          <h3 class="font-semibold block text-lg">
            Customization
            <pro-tag/>
          </h3>

          <p class="text-gray-400 mb-5">
            Change your form field name, pre-fill a value, add hints.
          </p>

          <text-input name="name" class="mt-4"
                      :form="field" :required="true"
                      label="Field Name"
          />
          <text-area-input name="prefill" class="mt-4"
                           :form="field"
                           label="Pre-filled value"
          />

          <!--    Placeholder    -->
          <text-input name="placeholder" class="mt-4"
                      :form="field"
                      label="Empty Input Text (Placeholder)"
          />

          <!--   Help  -->
          <text-input name="help" class="mt-4"
                      :form="field"
                      label="Field Help"

                      help="Your field help will be shown below the field, just like this message."
          />
        </div>
      </div>
      <div v-else-if="field.type == 'nf-divider'" class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <text-input name="name" class="mt-4"
                    :form="field" :required="true"
                    label="Field Name"
        />
      </div>
      <div v-else-if="field.type == 'nf-image'" class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <text-input name="name" class="mt-4"
                    :form="field" :required="true"
                    label="Field Name"
        />
        <image-input name="image_block" class="mt-4"
                     :form="field" label="Upload Image" :required="false"
        />
      </div>
      <div v-else class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <p>No settings found.</p>
      </div>

      <!--  Logic Block -->
      <form-block-logic-editor :form="form" :field="field" v-model="form"/>

      <div class="pt-5 text-right">
        <v-button color="gray" shade="light" @click="close">
          Close
        </v-button>
      </div>
    </div>
    <div v-else class="text-center p-10">
      Field not found.
    </div>
  </modal>
</template>

<script>
import ProTag from '../../../common/ProTag'
import FormBlockLogicEditor from '../components/form-logic-components/FormBlockLogicEditor'

export default {
  name: 'FormBlockOptionsModal',
  components: {ProTag, FormBlockLogicEditor},
  props: {
    field: {
      type: Object,
      required: false
    },
    form: {
      type: Object,
      required: false
    },
    show: {
      type: Boolean,
      required: false
    }
  },
  data() {
    return {}
  },

  computed: {},

  watch: {},

  mounted() {

  },

  methods: {
    close() {
      this.$emit('close')
    },
    removeBlock() {
      this.close()
      this.$emit('remove-block', this.field)
    },
    duplicateBlock(){
      this.close()
      this.$emit('duplicate-block', this.field)
    },
    onFieldRequiredChange(val) {
      this.$set(this.field, 'required', val)
      if (this.field.required) {
        this.$set(this.field, 'hidden', false)
      }
    },
    onFieldHiddenChange(val) {
      this.$set(this.field, 'hidden', val)
      if (this.field.hidden) {
        this.$set(this.field, 'required', false)
      }
    }
  }
}
</script>
