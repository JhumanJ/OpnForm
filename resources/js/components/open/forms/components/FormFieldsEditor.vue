<template>
  <div>
    <v-button class="w-full mb-5" @click="showAddBlock=true">
      Add Block
    </v-button>
    <add-form-block-modal :form-blocks="formFields" :show="showAddBlock" @block-added="blockAdded"
                          @close="showAddBlock=false"
    />
    <template v-if="selectedFieldIndex !== null">
      <form-field-options-modal :field="formFields[selectedFieldIndex]"
                                :show="!isNotAFormField(formFields[selectedFieldIndex]) && showEditFieldModal"
                                :form="form" @close="closeInputOptionModal"
                                @remove-block="removeBlock(selectedFieldIndex)"
      />
      <form-block-options-modal :field="formFields[selectedFieldIndex]"
                                :show="isNotAFormField(formFields[selectedFieldIndex]) && showEditFieldModal"
                                :form="form"
                                @remove-block="removeBlock(selectedFieldIndex)" @close="closeInputOptionModal"
      />
    </template>

    <draggable v-model="formFields"
               class="border bg-white dark:bg-notion-dark-light border-nt-blue-light shadow rounded-md w-full mx-auto transition-colors overflow-hidden"
               ghost-class="bg-nt-blue-lighter" handle=".draggable" :animation="200"
    >
      <div v-for="(field,index) in formFields" :key="field.id"
           class="border-nt-blue-light w-full mx-auto transition-colors bg-white dark:bg-notion-dark-light"
           :class="{'bg-gray-200 dark:bg-gray-800':field.hidden, 'border-b': (index!== formFields.length -1), 'bg-blue-50 dark:bg-blue-900':field && field.type==='nf-page-break'}"
      >
        <div v-if="field" class="flex items-center space-x-1 group py-2 pr-4">
          <!-- Drag handler -->
          <div class="cursor-move draggable p-2 -mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
              />
            </svg>
          </div>
          <!-- Field name and type -->
          <div class="flex flex-col flex-grow truncate">

            <editable-div class="truncate" :value="field.name" @input="onChangeName(field, $event)">
              <label class="cursor-pointer truncate w-full">
                {{ field.name }}
              </label>

              <span v-if="field.required" class="text-red-500 required-dot">*</span>
              <svg v-if="field.hidden" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                />
              </svg>
            </editable-div>

            <p class="text-xs text-gray-400 w-full truncate pl-2">
              <span class="capitalize">{{ formatType(field) }}</span>
            </p>
            <template slot="popover">
              <p class="text-white">
                {{ field.name }}
              </p>
            </template>
          </div>
          <!-- Field options -->

          <div class="flex-grow" v-if="['files'].includes(field.type) || field.type.startsWith('nf-')">
            <pro-tag/>
          </div>

          <button v-if="!field.type.startsWith('nf-')"
                  class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-1 hidden md:group-hover:block"
                  :class="{'text-blue-500': !field.hidden, 'text-gray-500': field.hidden}"
                  @click="toggleHidden(field)"
          >
            <template v-if="!field.hidden">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd"
                      d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                      clip-rule="evenodd"
                />
              </svg>
            </template>
            <template v-else>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
                      clip-rule="evenodd"
                />
                <path
                  d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"
                />
              </svg>
            </template>
          </button>
          <button v-if="!field.type.startsWith('nf-')"
                  class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-1 hidden md:group-hover:block"
                  @click="toggleRequired(field)"
          >
            <div class="w-6 h-6 text-center font-bold text-3xl"
                 :class="{'text-red-500': field.required, 'text-gray-500': !field.required}"
            >
              *
            </div>
          </button>
          <button class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-1"
                  @click="editOptions(index)"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-nt-blue" viewBox="0 0 20 20"
                 fill="currentColor"
            >
              <path fill-rule="evenodd"
                    d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                    clip-rule="evenodd"
              />
            </svg>
          </button>
        </div>
      </div>
    </draggable>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import FormFieldOptionsModal from '../fields/FormFieldOptionsModal'
import AddFormBlockModal from './form-components/AddFormBlockModal'
import FormBlockOptionsModal from '../fields/FormBlockOptionsModal'
import ProTag from '../../../common/ProTag'
import clonedeep from 'clone-deep'
import EditableDiv from '../../../common/EditableDiv'

export default {
  name: 'FormFieldsEditor',
  components: {
    ProTag,
    FormBlockOptionsModal,
    AddFormBlockModal,
    FormFieldOptionsModal,
    draggable,
    EditableDiv
  },

  data() {
    return {
      formFields: [],
      selectedFieldIndex: null,
      showEditFieldModal: false,
      showAddBlock: false
    }
  },

  computed: {
    form: {
      get() {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set(value) {
        this.$store.commit('open/working_form/set', value)
      }
    },
  },

  watch: {
    formFields: {
      deep: true,
      handler() {
        this.$set(this.form, 'properties', this.formFields)
      }
    }
  },

  mounted() {
    this.init()
  },

  methods: {
    onChangeName(field, newName) {
      this.$set(field, 'name', newName)
    },
    toggleHidden(field) {
      this.$set(field, 'hidden', !field.hidden)
      if (field.hidden) {
        this.$set(field, 'required', false)
      } else {
        this.$set(field, 'generates_uuid', false)
        this.$set(field, 'generates_auto_increment_id', false)
      }
    },
    toggleRequired(field) {
      this.$set(field, 'required', !field.required)
      if (field.required) {
        this.$set(field, 'hidden', false)
      }
    },
    getDefaultFields() {
      return [
        {
          "name": "Name",
          "type": "text",
          "hidden": false,
          "required": true,
          "id": this.generateUUID(),
        },
        {
          "name": "Email",
          "type": "email",
          "hidden": false,
          "id": this.generateUUID(),
        },
        {
          "name": "Message",
          "type": "text",
          "hidden": false,
          "multi_lines": true,
          "id": this.generateUUID(),
        }
      ];
    },
    init() {
      if (this.$route.name === 'forms.create') {  // Set Default fields
        this.formFields = this.getDefaultFields()
      } else {
        this.formFields = clonedeep(this.form.properties).map((field) => {
          // Add more field properties
          field.placeholder = field.placeholder || null
          field.prefill = field.prefill || null
          field.help = field.help || null

          return field
        })
      }
      this.$set(this.form, 'properties', this.formFields)
    },
    generateUUID() {
      let d = new Date().getTime()// Timestamp
      let d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now() * 1000)) || 0// Time in microseconds since page-load or 0 if unsupported
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        let r = Math.random() * 16// random number between 0 and 16
        if (d > 0) { // Use timestamp until depleted
          r = (d + r) % 16 | 0
          d = Math.floor(d / 16)
        } else { // Use microseconds since page-load if supported
          r = (d2 + r) % 16 | 0
          d2 = Math.floor(d2 / 16)
        }
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16)
      })
    },
    formatType(field) {
      let type = field.type.replace('_', ' ')
      if (!type.startsWith('nf')) {
        type = type + ' Input'
      } else {
        type = type.replace('nf-', '')
      }
      if (field.generates_uuid || field.generates_auto_increment_id) {
        type = type + ' - Auto ID'
      }
      return type
    },
    isNotAFormField(block) {
      return block && block.type.startsWith('nf')
    },
    editOptions(index) {
      this.selectedFieldIndex = index
      this.showEditFieldModal = true
    },
    blockAdded(block) {
      this.formFields.push(block)
    },
    removeBlock(blockIndex) {
      this.closeInputOptionModal()
      this.selectedFieldIndex = null
      const newFields = clonedeep(this.formFields)
      newFields.splice(blockIndex, 1)
      this.$set(this, 'formFields', newFields)
    },
    closeInputOptionModal() {
      this.showEditFieldModal = false
    }
  }
}
</script>

<style lang="scss">
.v-popover {
  .trigger {
    @apply truncate w-full;
  }
}
</style>
