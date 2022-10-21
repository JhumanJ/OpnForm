<template>
  <div>
    <add-form-block-modal :form-blocks="formFields" :show="showAddBlock" @block-added="blockAdded"
                          @close="showAddBlock=false"
    />
    <template v-if="selectedFieldIndex !== null">
      <form-field-options-modal :field="formFields[selectedFieldIndex]"
                                :show="!isNotAFormField(formFields[selectedFieldIndex]) && showEditFieldModal"
                                :form="form" @close="closeInputOptionModal"
                                @remove-block="removeBlock(selectedFieldIndex)"
                                @duplicate-block="duplicateBlock(selectedFieldIndex)"
      />
      <form-block-options-modal :field="formFields[selectedFieldIndex]"
                                :show="isNotAFormField(formFields[selectedFieldIndex]) && showEditFieldModal"
                                :form="form"
                                @remove-block="removeBlock(selectedFieldIndex)" 
                                @duplicate-block="duplicateBlock(selectedFieldIndex)" @close="closeInputOptionModal"
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
            <svg  class="h-4 w-4 text-gray-400" viewBox="0 0 18 8" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.5 1.0835H16.5M1.5 6.91683H16.5" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
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
          <button class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-1" @click="editOptions(index)">
            <svg class="h-6 w-6 text-nt-blue" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6.82927 16.1424L7.31631 17.2378C7.46109 17.5639 7.69737 17.8409 7.9965 18.0353C8.29562 18.2298 8.64473 18.3332 9.0015 18.3332C9.35826 18.3332 9.70737 18.2298 10.0065 18.0353C10.3056 17.8409 10.5419 17.5639 10.6867 17.2378L11.1737 16.1424C11.3471 15.7538 11.6387 15.4297 12.0071 15.2165C12.3777 15.0027 12.8065 14.9116 13.2321 14.9563L14.4237 15.0832C14.7784 15.1207 15.1364 15.0545 15.4543 14.8926C15.7721 14.7307 16.0362 14.4801 16.2145 14.1711C16.3929 13.8623 16.478 13.5084 16.4592 13.1522C16.4405 12.7961 16.3188 12.453 16.1089 12.1647L15.4033 11.1952C15.1521 10.8474 15.0179 10.4289 15.02 9.99984C15.0199 9.57199 15.1554 9.15513 15.4071 8.8091L16.1126 7.83965C16.3225 7.5513 16.4442 7.20823 16.4629 6.85207C16.4817 6.4959 16.3966 6.14195 16.2182 5.83317C16.0399 5.5242 15.7758 5.27358 15.458 5.11169C15.1401 4.94981 14.7821 4.88361 14.4274 4.92113L13.2358 5.04799C12.8102 5.09268 12.3814 5.00161 12.0108 4.7878C11.6417 4.57338 11.35 4.24764 11.1774 3.85725L10.6867 2.76187C10.5419 2.43581 10.3056 2.15877 10.0065 1.96434C9.70737 1.76991 9.35826 1.66645 9.0015 1.6665C8.64473 1.66645 8.29562 1.76991 7.9965 1.96434C7.69737 2.15877 7.46109 2.43581 7.31631 2.76187L6.82927 3.85725C6.65671 4.24764 6.365 4.57338 5.99594 4.7878C5.62529 5.00161 5.1965 5.09268 4.77094 5.04799L3.57557 4.92113C3.22084 4.88361 2.86285 4.94981 2.545 5.11169C2.22714 5.27358 1.96308 5.5242 1.78483 5.83317C1.60635 6.14195 1.52131 6.4959 1.54005 6.85207C1.55879 7.20823 1.68049 7.5513 1.89038 7.83965L2.59594 8.8091C2.84756 9.15513 2.98305 9.57199 2.98298 9.99984C2.98305 10.4277 2.84756 10.8445 2.59594 11.1906L1.89038 12.16C1.68049 12.4484 1.55879 12.7914 1.54005 13.1476C1.52131 13.5038 1.60635 13.8577 1.78483 14.1665C1.96326 14.4753 2.22735 14.7258 2.54516 14.8877C2.86297 15.0495 3.22087 15.1158 3.57557 15.0785L4.76724 14.9517C5.19279 14.907 5.62158 14.9981 5.99224 15.2119C6.36268 15.4257 6.65574 15.7515 6.82927 16.1424Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9.00001 12.4998C10.3807 12.4998 11.5 11.3805 11.5 9.99984C11.5 8.61913 10.3807 7.49984 9.00001 7.49984C7.61929 7.49984 6.50001 8.61913 6.50001 9.99984C6.50001 11.3805 7.61929 12.4998 9.00001 12.4998Z" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
    </draggable>

    <button 
      class="mt-3 group cursor-pointer relative w-full rounded-lg border-transparent flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-gray-50 text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click="showAddBlock=true">
      <svg class="w-4 h-4 text-nt-blue inline mr-1" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M7.00001 1.1665V12.8332M1.16667 6.99984H12.8333" stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round"/>
      </svg> Add block
    </button>

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
        this.formFields = (this.form.properties.length > 0) ? clonedeep(this.form.properties): this.getDefaultFields()
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
    duplicateBlock(blockIndex) {
      this.closeInputOptionModal()
      this.selectedFieldIndex = null
      const newField = clonedeep(this.formFields[blockIndex])
      newField.id = this.generateUUID()
      this.formFields.push(newField)
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
