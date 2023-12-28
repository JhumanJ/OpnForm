<template>
  <div v-if="field">
    <!-- General -->
    <div class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        General
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Exclude this field or make it required.
      </p>
      <v-checkbox v-model="field.hidden" class="mb-3"
                  :name="field.id+'_hidden'"
                  @update:model-value="onFieldHiddenChange"
      >
        Hidden
      </v-checkbox>
      <select-input name="width" class="mt-3"
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
      <select-input v-if="['nf-text','nf-image'].includes(field.type)" name="align" class="mt-3"
                    :options="[
                      {name:'Left',value:'left'},
                      {name:'Center',value:'center'},
                      {name:'Right',value:'right'},
                      {name:'Justify',value:'justify'}
                    ]"
                    :form="field" label="Field Alignment"
      />
    </div>

    <div v-if="field.type == 'nf-text'" class="border-b py-2 px-4">
      <rich-text-area-input name="content"
                            :form="field"
                            label="Content"
                            :required="false"
      />
    </div>

    <div v-else-if="field.type == 'nf-page-break'" class="border-b py-2 px-4">
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

    <div v-else-if="field.type == 'nf-divider'" class="border-b py-2 px-4">
      <text-input name="name"
                  :form="field" :required="true"
                  label="Field Name"
      />
    </div>

    <div v-else-if="field.type == 'nf-image'" class="border-b py-2 px-4">
      <text-input name="name"
                  :form="field" :required="true"
                  label="Field Name"
      />
      <image-input name="image_block" class="mt-3"
                   :form="field" label="Upload Image" :required="false"
      />
    </div>

    <div v-else-if="field.type == 'nf-code'" class="border-b py-2 px-4">
      <code-input name="content" :form="field" label="Content"
                  help="You can add any html code, including iframes"
      />
    </div>

    <div v-else class="border-b py-2 px-4">
      <p>No settings found.</p>
    </div>

    <!--  Logic Block -->
    <form-block-logic-editor class="py-2 px-4 border-b" :form="form" :field="field" />
  </div>
</template>

<script>
import FormBlockLogicEditor from '../../components/form-logic-components/FormBlockLogicEditor.vue'

export default {
  name: 'BlockOptions',
  components: {FormBlockLogicEditor},
  props: {
    field: {
      type: Object,
      required: false
    },
    form: {
      type: Object,
      required: false
    }
  },
  data () {
    return {
      editorToolbarCustom: [
        ['bold', 'italic', 'underline', 'link']
      ]
    }
  },

  computed: {},

  watch: {
    'field.width': {
      handler (val) {
        if (val === undefined || val === null) {
          this.field.width = 'full'
        }
      },
      immediate: true
    },
    'field.align': {
      handler (val) {
        if (val === undefined || val === null) {
          this.field.align = 'left'
        }
      },
      immediate: true
    }
  },

  created () {
    if (this.field?.width === undefined || this.field?.width === null) {
      this.field.width = 'full'
    }
  },

  mounted () {},

  methods: {
    onFieldHiddenChange (val) {
      this.field.hidden = val
      if (this.field.hidden) {
        this.field.required = false
      }
    },
    onFieldHelpPositionChange (val) {
      if (!val) {
        this.field.help_position = 'below_input'
      }
    }
  }
}
</script>
