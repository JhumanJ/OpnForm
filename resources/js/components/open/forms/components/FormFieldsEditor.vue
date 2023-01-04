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
               class="bg-white overflow-hidden dark:bg-notion-dark-light rounded-md w-full mx-auto border transition-colors"
               ghost-class="bg-gray-50" handle=".draggable" :animation="200"
    >
      <div v-for="(field,index) in formFields" :key="field.id"
           class="w-full mx-auto transition-colors"
           :class="{'bg-gray-100 dark:bg-gray-800':field.hidden,'bg-white dark:bg-notion-dark-light':!field.hidden && !field.type==='nf-page-break', 'border-b': (index!== formFields.length -1), 'bg-blue-50 dark:bg-blue-900':field && field.type==='nf-page-break'}"
      >
        <div v-if="field" class="flex items-center space-x-1 group py-2 pr-4 relative">
          <!-- Drag handler -->
          <div class="cursor-move draggable p-2 -mr-2">
            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 18 8" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M1.5 1.0835H16.5M1.5 6.91683H16.5" stroke="currentColor" stroke-width="1.67"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <!-- Field name and type -->
          <div class="flex flex-col flex-grow truncate">

            <editable-div class="max-w-full flex items-center" :value="field.name" @input="onChangeName(field, $event)">
              <div class="cursor-pointer max-w-full truncate">
                {{ field.name }}
              </div>
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

          <!--          <div class="flex-grow" v-if="['files'].includes(field.type) || field.type.startsWith('nf-')">-->
          <!--            <pro-tag/>-->
          <!--          </div>-->

          <template v-if="removing == field.id">
            <div class="flex text-sm items-center">
              Remove block?
              <v-button class="inline ml-1" color="red" size="small" @click="removeBlock(index)">Yes</v-button>
              <v-button @click="removing=false" class="inline ml-1" color="light-gray" size="small">No</v-button>
            </div>
          </template>
          <template v-else>
            <button v-if="!field.type.startsWith('nf-')"
                    class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-2 hidden"
                    :class="{'text-blue-500': !field.hidden, 'text-gray-500': field.hidden, 'group-hover:md:block': !field.hidden, 'md:block':field.hidden}"
                    @click="toggleHidden(field)"
            >
              <template v-if="!field.hidden">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path
                    d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </template>
              <template v-else>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0_1027_7292)">
                    <path
                      d="M9.9 4.24C10.5883 4.07888 11.2931 3.99834 12 4C19 4 23 12 23 12C22.393 13.1356 21.6691 14.2047 20.84 15.19M14.12 14.12C13.8454 14.4147 13.5141 14.6512 13.1462 14.8151C12.7782 14.9791 12.3809 15.0673 11.9781 15.0744C11.5753 15.0815 11.1752 15.0074 10.8016 14.8565C10.4281 14.7056 10.0887 14.481 9.80385 14.1962C9.51897 13.9113 9.29439 13.5719 9.14351 13.1984C8.99262 12.8248 8.91853 12.4247 8.92563 12.0219C8.93274 11.6191 9.02091 11.2218 9.18488 10.8538C9.34884 10.4859 9.58525 10.1546 9.88 9.88M1 1L23 23M17.94 17.94C16.2306 19.243 14.1491 19.9649 12 20C5 20 1 12 1 12C2.24389 9.6819 3.96914 7.65661 6.06 6.06L17.94 17.94Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </g>
                  <defs>
                    <clipPath id="clip0_1027_7292">
                      <rect width="24" height="24" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>

              </template>
            </button>
            <button v-if="!field.type.startsWith('nf-')"
                    class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-2 hidden"
                    @click="toggleRequired(field)" :class="{'group-hover:md:block': !field.required, 'md:block':field.required}"
            >
              <div class="w-4 h-4 text-center font-bold text-3xl"
                   :class="{'text-red-500': field.required, 'text-gray-500': !field.required}"
              >
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12 2V12M12 12V22M12 12L4.93 4.93M12 12L19.07 19.07M12 12H2M12 12H22M12 12L4.93 19.07M12 12L19.07 4.93"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </button>
            <button
              class="hover:bg-red-50 text-gray-500 hover:text-red-600 rounded transition-colors cursor-pointer p-2 hidden md:group-hover:block"
              @click="removing=field.id"
            >
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6H5ZM8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M10 11V17M14 11V17"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>

            </button>
            <button class="hover:bg-nt-blue-lighter rounded transition-colors cursor-pointer p-2"
                    @click="editOptions(index)">
              <svg class="h-4 w-4 text-blue-600" width="24" height="24" viewBox="0 0 24 24" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_1027_7210)">
                  <path
                    d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path
                    d="M19.4 15C19.2669 15.3016 19.2272 15.6362 19.286 15.9606C19.3448 16.285 19.4995 16.5843 19.73 16.82L19.79 16.88C19.976 17.0657 20.1235 17.2863 20.2241 17.5291C20.3248 17.7719 20.3766 18.0322 20.3766 18.295C20.3766 18.5578 20.3248 18.8181 20.2241 19.0609C20.1235 19.3037 19.976 19.5243 19.79 19.71C19.6043 19.896 19.3837 20.0435 19.1409 20.1441C18.8981 20.2448 18.6378 20.2966 18.375 20.2966C18.1122 20.2966 17.8519 20.2448 17.6091 20.1441C17.3663 20.0435 17.1457 19.896 16.96 19.71L16.9 19.65C16.6643 19.4195 16.365 19.2648 16.0406 19.206C15.7162 19.1472 15.3816 19.1869 15.08 19.32C14.7842 19.4468 14.532 19.6572 14.3543 19.9255C14.1766 20.1938 14.0813 20.5082 14.08 20.83V21C14.08 21.5304 13.8693 22.0391 13.4942 22.4142C13.1191 22.7893 12.6104 23 12.08 23C11.5496 23 11.0409 22.7893 10.6658 22.4142C10.2907 22.0391 10.08 21.5304 10.08 21V20.91C10.0723 20.579 9.96512 20.258 9.77251 19.9887C9.5799 19.7194 9.31074 19.5143 9 19.4C8.69838 19.2669 8.36381 19.2272 8.03941 19.286C7.71502 19.3448 7.41568 19.4995 7.18 19.73L7.12 19.79C6.93425 19.976 6.71368 20.1235 6.47088 20.2241C6.22808 20.3248 5.96783 20.3766 5.705 20.3766C5.44217 20.3766 5.18192 20.3248 4.93912 20.2241C4.69632 20.1235 4.47575 19.976 4.29 19.79C4.10405 19.6043 3.95653 19.3837 3.85588 19.1409C3.75523 18.8981 3.70343 18.6378 3.70343 18.375C3.70343 18.1122 3.75523 17.8519 3.85588 17.6091C3.95653 17.3663 4.10405 17.1457 4.29 16.96L4.35 16.9C4.58054 16.6643 4.73519 16.365 4.794 16.0406C4.85282 15.7162 4.81312 15.3816 4.68 15.08C4.55324 14.7842 4.34276 14.532 4.07447 14.3543C3.80618 14.1766 3.49179 14.0813 3.17 14.08H3C2.46957 14.08 1.96086 13.8693 1.58579 13.4942C1.21071 13.1191 1 12.6104 1 12.08C1 11.5496 1.21071 11.0409 1.58579 10.6658C1.96086 10.2907 2.46957 10.08 3 10.08H3.09C3.42099 10.0723 3.742 9.96512 4.0113 9.77251C4.28059 9.5799 4.48572 9.31074 4.6 9C4.73312 8.69838 4.77282 8.36381 4.714 8.03941C4.65519 7.71502 4.50054 7.41568 4.27 7.18L4.21 7.12C4.02405 6.93425 3.87653 6.71368 3.77588 6.47088C3.67523 6.22808 3.62343 5.96783 3.62343 5.705C3.62343 5.44217 3.67523 5.18192 3.77588 4.93912C3.87653 4.69632 4.02405 4.47575 4.21 4.29C4.39575 4.10405 4.61632 3.95653 4.85912 3.85588C5.10192 3.75523 5.36217 3.70343 5.625 3.70343C5.88783 3.70343 6.14808 3.75523 6.39088 3.85588C6.63368 3.95653 6.85425 4.10405 7.04 4.29L7.1 4.35C7.33568 4.58054 7.63502 4.73519 7.95941 4.794C8.28381 4.85282 8.61838 4.81312 8.92 4.68H9C9.29577 4.55324 9.54802 4.34276 9.72569 4.07447C9.90337 3.80618 9.99872 3.49179 10 3.17V3C10 2.46957 10.2107 1.96086 10.5858 1.58579C10.9609 1.21071 11.4696 1 12 1C12.5304 1 13.0391 1.21071 13.4142 1.58579C13.7893 1.96086 14 2.46957 14 3V3.09C14.0013 3.41179 14.0966 3.72618 14.2743 3.99447C14.452 4.26276 14.7042 4.47324 15 4.6C15.3016 4.73312 15.6362 4.77282 15.9606 4.714C16.285 4.65519 16.5843 4.50054 16.82 4.27L16.88 4.21C17.0657 4.02405 17.2863 3.87653 17.5291 3.77588C17.7719 3.67523 18.0322 3.62343 18.295 3.62343C18.5578 3.62343 18.8181 3.67523 19.0609 3.77588C19.3037 3.87653 19.5243 4.02405 19.71 4.21C19.896 4.39575 20.0435 4.61632 20.1441 4.85912C20.2448 5.10192 20.2966 5.36217 20.2966 5.625C20.2966 5.88783 20.2448 6.14808 20.1441 6.39088C20.0435 6.63368 19.896 6.85425 19.71 7.04L19.65 7.1C19.4195 7.33568 19.2648 7.63502 19.206 7.95941C19.1472 8.28381 19.1869 8.61838 19.32 8.92V9C19.4468 9.29577 19.6572 9.54802 19.9255 9.72569C20.1938 9.90337 20.5082 9.99872 20.83 10H21C21.5304 10 22.0391 10.2107 22.4142 10.5858C22.7893 10.9609 23 11.4696 23 12C23 12.5304 22.7893 13.0391 22.4142 13.4142C22.0391 13.7893 21.5304 14 21 14H20.91C20.5882 14.0013 20.2738 14.0966 20.0055 14.2743C19.7372 14.452 19.5268 14.7042 19.4 15Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                  <clipPath id="clip0_1027_7210">
                    <rect width="24" height="24" fill="white"/>
                  </clipPath>
                </defs>
              </svg>
            </button>
          </template>
        </div>
      </div>
    </draggable>

    <v-button
      class="w-full mt-4" color="light-gray"
      @click="showAddBlock=true">
      <svg class="w-4 h-4 text-nt-blue inline mr-1 -mt-1" viewBox="0 0 14 14" fill="none"
           xmlns="http://www.w3.org/2000/svg">
        <path d="M7.00001 1.1665V12.8332M1.16667 6.99984H12.8333" stroke="currentColor" stroke-width="1.67"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Add block
    </v-button>

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
import VButton from "../../../common/Button";

export default {
  name: 'FormFieldsEditor',
  components: {
    VButton,
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
      showAddBlock: false,
      removing: null
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
      if (this.$route.name === 'forms.create' || this.$route.name === 'forms.create.guest') {  // Set Default fields
        this.formFields = (this.form.properties.length > 0) ? clonedeep(this.form.properties) : this.getDefaultFields()
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
