<template>
  <modal :show="show" @close="close">
    <div v-if="field">
      <div class="flex">
        <h2 class="text-2xl font-semibold z-10 truncate mb-5 text-gray-900 flex-grow">
          Configure "<span class="truncate">{{ field.name }}</span>" block
        </h2>
        <div class="flex mr-6">
          <div>
            <v-button color="light-gray" size="small" @click="removeBlock">
              <svg class="h-4 w-4 text-red-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6H5ZM8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M10 11V17M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>

              Remove
            </v-button>
          </div>
          <div class="ml-1">
            <v-button size="small" color="light-gray" @click="duplicateBlock">
              <svg class="h-4 w-4 text-blue-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 15H4C3.46957 15 2.96086 14.7893 2.58579 14.4142C2.21071 14.0391 2 13.5304 2 13V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H13C13.5304 2 14.0391 2.21071 14.4142 2.58579C14.7893 2.96086 15 3.46957 15 4V5M11 9H20C21.1046 9 22 9.89543 22 11V20C22 21.1046 21.1046 22 20 22H11C9.89543 22 9 21.1046 9 20V11C9 9.89543 9.89543 9 11 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Duplicate
            </v-button>
          </div>
        </div>
      </div>

      <div class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <div class="-mx-4 sm:-mx-6 px-5 pt-0">
          <h3 class="font-semibold block text-lg">
            General
          </h3>
          <p class="text-gray-400 mb-5 text-xs">
            Exclude this field or make it required.
          </p>
          <v-checkbox v-model="field.hidden" class="mb-3"
                      :name="field.id+'_hidden'"
                      @input="onFieldHiddenChange"
          >
            Hidden
          </v-checkbox>
          <select-input name="width" class="mt-4"
                        :options="[
                          {name:'Full',value:'full'},
                          {name:'1/2 (half width)',value:'1/2'},
                          {name:'1/3 (a third of the width)',value:'1/3'},
                          {name:'2/3 (two thirds of the width)',value:'2/3'},
                          {name:'1/4 (a quarter of the width)',value:'1/4'},
                          {name:'3/4 (three quarters of the width)',value:'3/4'}
                        ]"
                        :form="field" label="Field Width"
          />
          <select-input v-if="['nf-text','nf-image'].includes(field.type)" name="align" class="mt-4"
                        :options="[
                          {name:'Left',value:'left'},
                          {name:'Center',value:'center'},
                          {name:'Right',value:'right'},
                          {name:'Justify',value:'justify'}
                        ]"
                        :form="field" label="Field Alignment"
          />
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
      <div v-else-if="field.type == 'nf-code'" class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <code-input name="content" class="mt-4 h-36" :form="field" label="Content"
              help="You can add any html code, including iframes" />
      </div>
      <div v-else class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <p>No settings found.</p>
      </div>

      <!--  Logic Block -->
      <form-block-logic-editor :form="form" :field="field" v-model="form"/>

      <div class="pt-5 flex justify-end">
        <v-button color="white" @click="close">
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
import ProTag from '../../../common/ProTag.vue'
const FormBlockLogicEditor = () => import('../components/form-logic-components/FormBlockLogicEditor.vue')
import CodeInput from '../../../forms/CodeInput.vue'

export default {
  name: 'FormBlockOptionsModal',
  components: {ProTag, FormBlockLogicEditor, CodeInput},
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
    return {
      editorToolbarCustom: [
        ['bold', 'italic', 'underline', 'link'],
      ]
    }
  },

  computed: {},

  watch: {
    'field.width': {
      handler (val) {
        if (val === undefined || val === null) {
          this.$set(this.field, 'width', 'full')
        }
      },
      immediate: true
    },
    'field.align': {
      handler (val) {
        if (val === undefined || val === null) {
          this.$set(this.field, 'align', 'left')
        }
      },
      immediate: true
    }
  },

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
    onFieldHiddenChange(val) {
      this.$set(this.field, 'hidden', val)
      if (this.field.hidden) {
        this.$set(this.field, 'required', false)
      }
    },
    onFieldHelpPositionChange (val) {
      if(!val){
        this.$set(this.field, 'help_position', 'below_input')
      }
    }
  }
}
</script>
