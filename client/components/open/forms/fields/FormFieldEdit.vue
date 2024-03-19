<template>
  <div>
    <div class="p-4 border-b sticky top-0 z-10 bg-white">
      <button v-if="!field" class="text-gray-500 hover:text-gray-900 cursor-pointer" @click.prevent="closeSidebar">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round"
          />
        </svg>
      </button>
      <template v-else>
        <div class="flex">
          <button class="text-gray-500 hover:text-gray-900 cursor-pointer" @click.prevent="closeSidebar">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round"
              />
            </svg>
          </button>
          <div class="font-semibold inline ml-2 truncate flex-grow truncate">
            Configure "<span class="truncate">{{ field.name }}</span>"
          </div>
        </div>
        <div class="flex mt-2">
          <v-button color="light-gray" class="border-r-0 rounded-r-none text-xs hover:bg-red-50" size="small"
                    @click="removeBlock"
          >
            <svg class="h-4 w-4 text-red-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6H5ZM8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M10 11V17M14 11V17"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              />
            </svg>
            Remove
          </v-button>
          <v-button size="small" class="text-xs" :class="{
            'rounded-none border-r-0':!isBlockField && typeCanBeChanged,
            'rounded-l-none':isBlockField || !typeCanBeChanged
          }" color="light-gray" @click="duplicateBlock"
          >
            <svg class="h-4 w-4 text-blue-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M5 15H4C3.46957 15 2.96086 14.7893 2.58579 14.4142C2.21071 14.0391 2 13.5304 2 13V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H13C13.5304 2 14.0391 2.21071 14.4142 2.58579C14.7893 2.96086 15 3.46957 15 4V5M11 9H20C21.1046 9 22 9.89543 22 11V20C22 21.1046 21.1046 22 20 22H11C9.89543 22 9 21.1046 9 20V11C9 9.89543 9.89543 9 11 9Z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              />
            </svg>
            Duplicate
          </v-button>
          <change-field-type v-if="!isBlockField" btn-classes="rounded-l-none text-xs" :field="field"
                             @changeType="onChangeType"
          />
        </div>
      </template>
    </div>

    <template v-if="field">
      <field-options v-if="!isBlockField" :form="form" :field="field" />
      <block-options v-if="isBlockField" :form="form" :field="field" />
    </template>
    <div v-else class="text-center p-10">
      Click on field's setting icon in your form to modify it
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import clonedeep from 'clone-deep'
import { useWorkingFormStore } from '../../../../stores/working_form'
import ChangeFieldType from './components/ChangeFieldType.vue'
import FieldOptions from './components/FieldOptions.vue'
import BlockOptions from './components/BlockOptions.vue'

export default {
  name: 'FormFieldEdit',
  components: { ChangeFieldType, FieldOptions, BlockOptions },
  props: {},
  setup () {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      selectedFieldIndex : computed(() => workingFormStore.selectedFieldIndex)
    }
  },
  data () {
    return {

    }
  },

  computed: {
    form: {
      get () {
        return this.workingFormStore.content
      },
      /* We add a setter */
      set (value) {
        this.workingFormStore.set(value)
      }
    },
    field () {
      return (this.form && this.selectedFieldIndex !== null) ? this.form.properties[this.selectedFieldIndex] : null
    },
    isBlockField () {
      return this.field && this.field.type.startsWith('nf')
    },
    typeCanBeChanged () {
      return ['text', 'email', 'phone_number', 'number', 'select', 'multi_select', 'rating', 'scale', 'slider'].includes(this.field.type)
    }
  },

  watch: {},

  created () {
  },

  mounted () {
  },

  methods: {
    onChangeType (newType) {
      if (['select', 'multi_select'].includes(this.field.type)) {
        this.field[newType] = this.field[this.field.type] // Set new options with new type
        delete this.field[this.field.type] // remove old type options
      }
      this.field.type = newType
    },
    removeBlock () {
      const newFields = clonedeep(this.form.properties)
      newFields.splice(this.selectedFieldIndex, 1)
      this.form.properties = newFields
      this.closeSidebar()
    },
    duplicateBlock () {
      const newFields = clonedeep(this.form.properties)
      const newField = clonedeep(this.form.properties[this.selectedFieldIndex])
      newField.id = this.generateUUID()
      newFields.push(newField)
      this.form.properties = newFields
      this.closeSidebar()
    },
    closeSidebar () {
      this.workingFormStore.closeEditFieldSidebar()
    },
    generateUUID () {
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
    }
  }
}
</script>
