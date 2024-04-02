<template>
  <div v-if="!isFieldHidden" :id="'block-' + field.id" :class="getFieldWidthClasses(field)">
    <div :class="getFieldClasses(field)">
      <div v-if="adminPreview"
        class="absolute -translate-x-full top-0 bottom-0 opacity-0 group-hover/nffield:opacity-100 transition-opacity mb-4">
        <div class="flex flex-col bg-white rounded-md"
          :class="{ 'lg:flex-row': !fieldSideBarOpened, 'xl:flex-row': fieldSideBarOpened }">
          <div class="p-2 -mr-3 -mb-2 text-gray-300 hover:text-blue-500 cursor-pointer hidden xl:block" role="button"
            :class="{ 'lg:block': !fieldSideBarOpened, 'xl:block': fieldSideBarOpened }"
            @click.prevent="openAddFieldSidebar">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
              stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
          </div>
          <div class="p-2 text-gray-300 hover:text-blue-500 cursor-pointer" role="button"
            :class="{ 'lg:-mr-2': !fieldSideBarOpened, 'xl:-mr-2': fieldSideBarOpened }"
            @click.prevent="editFieldOptions">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
              <path fill-rule="evenodd"
                d="M11.828 2.25c-.916 0-1.699.663-1.85 1.567l-.091.549a.798.798 0 01-.517.608 7.45 7.45 0 00-.478.198.798.798 0 01-.796-.064l-.453-.324a1.875 1.875 0 00-2.416.2l-.243.243a1.875 1.875 0 00-.2 2.416l.324.453a.798.798 0 01.064.796 7.448 7.448 0 00-.198.478.798.798 0 01-.608.517l-.55.092a1.875 1.875 0 00-1.566 1.849v.344c0 .916.663 1.699 1.567 1.85l.549.091c.281.047.508.25.608.517.06.162.127.321.198.478a.798.798 0 01-.064.796l-.324.453a1.875 1.875 0 00.2 2.416l.243.243c.648.648 1.67.733 2.416.2l.453-.324a.798.798 0 01.796-.064c.157.071.316.137.478.198.267.1.47.327.517.608l.092.55c.15.903.932 1.566 1.849 1.566h.344c.916 0 1.699-.663 1.85-1.567l.091-.549a.798.798 0 01.517-.608 7.52 7.52 0 00.478-.198.798.798 0 01.796.064l.453.324a1.875 1.875 0 002.416-.2l.243-.243c.648-.648.733-1.67.2-2.416l-.324-.453a.798.798 0 01-.064-.796c.071-.157.137-.316.198-.478.1-.267.327-.47.608-.517l.55-.091a1.875 1.875 0 001.566-1.85v-.344c0-.916-.663-1.699-1.567-1.85l-.549-.091a.798.798 0 01-.608-.517 7.507 7.507 0 00-.198-.478.798.798 0 01.064-.796l.324-.453a1.875 1.875 0 00-.2-2.416l-.243-.243a1.875 1.875 0 00-2.416-.2l-.453.324a.798.798 0 01-.796.064 7.462 7.462 0 00-.478-.198.798.798 0 01-.517-.608l-.091-.55a1.875 1.875 0 00-1.85-1.566h-.344zM12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z"
                clip-rule="evenodd" />
            </svg>
          </div>
          <div
            class="px-2 xl:pl-0 lg:pr-1 lg:pt-2 pb-2 bg-white rounded-md text-gray-300 hover:text-gray-500 cursor-grab draggable"
            :class="{ 'lg:pr-1 lg:pl-0': !fieldSideBarOpened, 'xl:-mr-2': fieldSideBarOpened }" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </div>
        </div>
      </div>
      <component :is="getFieldComponents" v-if="getFieldComponents" v-bind="inputProperties(field)"
        :required="isFieldRequired" :disabled="isFieldDisabled ? true : null" />
      <template v-else>
        <div v-if="field.type === 'nf-text' && field.content" :id="field.id" :key="field.id" class="nf-text w-full mb-3"
          :class="[getFieldAlignClasses(field)]" v-html="field.content" />
        <div v-if="field.type === 'nf-code' && field.content" :id="field.id" :key="field.id"
          class="nf-code w-full px-2 mb-3" v-html="field.content" />
        <div v-if="field.type === 'nf-divider'" :id="field.id" :key="field.id" class="border-b my-4 w-full mx-2" />
        <div v-if="field.type === 'nf-image' && (field.image_block || !isPublicFormPage)" :id="field.id" :key="field.id"
          class="my-4 w-full px-2" :class="[getFieldAlignClasses(field)]">
          <div v-if="!field.image_block" class="p-4 border border-dashed">
            Open <b>{{ field.name }}'s</b> block settings to upload image.
          </div>
          <img v-else :alt="field.name" :src="field.image_block" class="max-w-full" />
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import FormLogicPropertyResolver from "~/lib/forms/FormLogicPropertyResolver.js"
import { default as _has } from 'lodash/has'

export default {
  name: 'OpenFormField',
  components: {},
  props: {
    form: {
      type: Object,
      required: true
    },
    dataForm: {
      type: Object,
      required: true
    },
    dataFormValue: {
      type: Object,
      required: true
    },
    theme: {
      type: Object,
      required: true
    },
    showHidden: {
      type: Boolean,
      default: false
    },
    field: {
      type: Object,
      required: true
    },
    adminPreview: { type: Boolean, default: false } // If used in FormEditorPreview
  },

  setup(props) {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      currentWorkspace: computed(() => useWorkspacesStore().getCurrent),
      selectedFieldIndex: computed(() => workingFormStore.selectedFieldIndex),
      showEditFieldSidebar: computed(() => workingFormStore.showEditFieldSidebar)
    }
  },

  computed: {
    /**
     * Get the right input component for the field/options combination
     */
    getFieldComponents() {
      const field = this.field
      if (field.type === 'text' && field.multi_lines) {
        return 'TextAreaInput'
      }
      if (field.type === 'url' && field.file_upload) {
        return 'FileInput'
      }
      if (['select', 'multi_select'].includes(field.type) && field.without_dropdown) {
        return 'FlatSelectInput'
      }
      if (field.type === 'checkbox' && field.use_toggle_switch) {
        return 'ToggleSwitchInput'
      }
      if (field.type === 'signature') {
        return 'SignatureInput'
      }
      if (field.type === 'phone_number' && !field.use_simple_text_input) {
        return 'PhoneInput'
      }
      return {
        text: 'TextInput',
        number: 'TextInput',
        rating: 'RatingInput',
        scale: 'ScaleInput',
        slider: 'SliderInput',
        select: 'SelectInput',
        multi_select: 'SelectInput',
        date: 'DateInput',
        files: 'FileInput',
        checkbox: 'CheckboxInput',
        url: 'TextInput',
        email: 'TextInput',
        phone_number: 'TextInput'
      }[field.type]
    },
    isPublicFormPage() {
      return this.$route.name === 'forms-slug'
    },
    isFieldHidden() {
      return !this.showHidden && this.shouldBeHidden
    },
    shouldBeHidden() {
      return (new FormLogicPropertyResolver(this.field, this.dataFormValue)).isHidden()
    },
    isFieldRequired() {
      return (new FormLogicPropertyResolver(this.field, this.dataFormValue)).isRequired()
    },
    isFieldDisabled() {
      return (new FormLogicPropertyResolver(this.field, this.dataFormValue)).isDisabled()
    },
    beingEdited() {
      return this.adminPreview && this.showEditFieldSidebar && this.form.properties.findIndex((item) => {
        return item.id === this.field.id
      }) === this.selectedFieldIndex
    },
    selectionFieldsOptions() {
      // For auto update hidden options
      let fieldsOptions = []

      if (['select', 'multi_select', 'status'].includes(this.field.type)) {
        fieldsOptions = [...this.field[this.field.type].options]
        if (this.field.hidden_options && this.field.hidden_options.length > 0) {
          fieldsOptions = fieldsOptions.filter((option) => {
            return this.field.hidden_options.indexOf(option.id) < 0
          })
        }
      }

      return fieldsOptions
    },
    fieldSideBarOpened() {
      return this.adminPreview && (this.form && this.selectedFieldIndex !== null) ? (this.form.properties[this.selectedFieldIndex] && this.showEditFieldSidebar) : false
    }
  },

  watch: {},

  mounted() {
  },

  methods: {
    editFieldOptions() {
      this.workingFormStore.openSettingsForField(this.field)
    },
    openAddFieldSidebar() {
      this.workingFormStore.openAddFieldSidebar(this.field)
    },
    /**
     * Get the right input component for the field/options combination
     */
    getFieldClasses() {
      let classes = ''
      if (this.adminPreview) {
        classes += '-mx-4 px-4 -my-1 py-1 group/nffield relative transition-colors'

        if (this.beingEdited) {
          classes += ' bg-blue-50 dark:bg-gray-800 rounded-md'
        }
      }
      return classes
    },
    getFieldWidthClasses(field) {
      if (!field.width || field.width === 'full') return 'w-full px-2'
      else if (field.width === '1/2') {
        return 'w-full sm:w-1/2 px-2'
      } else if (field.width === '1/3') {
        return 'w-full sm:w-1/3 px-2'
      } else if (field.width === '2/3') {
        return 'w-full sm:w-2/3 px-2'
      } else if (field.width === '1/4') {
        return 'w-full sm:w-1/4 px-2'
      } else if (field.width === '3/4') {
        return 'w-full sm:w-3/4 px-2'
      }
    },
    getFieldAlignClasses(field) {
      if (!field.align || field.align === 'left') return 'text-left'
      else if (field.align === 'right') {
        return 'text-right'
      } else if (field.align === 'center') {
        return 'text-center'
      } else if (field.align === 'justify') {
        return 'text-justify'
      }
    },
    /**
     * Get the right input component options for the field/options
     */
    inputProperties(field) {
      const inputProperties = {
        key: field.id,
        name: field.id,
        form: this.dataForm,
        label: (field.hide_field_name) ? null : field.name + ((this.shouldBeHidden) ? ' (Hidden Field)' : ''),
        color: this.form.color,
        placeholder: field.placeholder,
        help: field.help,
        helpPosition: (field.help_position) ? field.help_position : 'below_input',
        uppercaseLabels: this.form.uppercase_labels == 1 || this.form.uppercase_labels == true,
        theme: this.theme,
        maxCharLimit: (field.max_char_limit) ? parseInt(field.max_char_limit) : 2000,
        showCharLimit: field.show_char_limit || false
      }

      if (['select', 'multi_select'].includes(field.type)) {
        inputProperties.options = (_has(field, field.type))
          ? field[field.type].options.map(option => {
            return {
              name: option.name,
              value: option.name
            }
          })
          : []
        inputProperties.multiple = (field.type === 'multi_select')
        inputProperties.allowCreation = (field.allow_creation === true)
        inputProperties.searchable = (inputProperties.options.length > 4)
      } else if (field.type === 'date') {
        if (field.with_time) {
          inputProperties.withTime = true
        } else if (field.date_range) {
          inputProperties.dateRange = true
        }
        if (field.disable_past_dates) {
          inputProperties.disablePastDates = true
        } else if (field.disable_future_dates) {
          inputProperties.disableFutureDates = true
        }
      } else if (field.type === 'files' || (field.type === 'url' && field.file_upload)) {
        inputProperties.multiple = (field.multiple !== undefined && field.multiple)
        inputProperties.cameraUpload = (field.camera_upload !== undefined && field.camera_upload)
        let maxFileSize = (this.form?.workspace && this.form?.workspace.max_file_size) ? this.form?.workspace?.max_file_size : 10
        if (field?.max_file_size > 0) {
          maxFileSize = Math.min(field.max_file_size, maxFileSize)
        }
        inputProperties.mbLimit = maxFileSize
        inputProperties.accept = (this.form.is_pro && field.allowed_file_types) ? field.allowed_file_types : ''
      } else if (field.type === 'rating') {
        inputProperties.numberOfStars = parseInt(field.rating_max_value) ?? 5
      } else if (field.type === 'scale') {
        inputProperties.minScale = parseInt(field.scale_min_value) ?? 1
        inputProperties.maxScale = parseInt(field.scale_max_value) ?? 5
        inputProperties.stepScale = parseInt(field.scale_step_value) ?? 1
      } else if (field.type === 'slider') {
        inputProperties.minSlider = parseInt(field.slider_min_value) ?? 0
        inputProperties.maxSlider = parseInt(field.slider_max_value) ?? 50
        inputProperties.stepSlider = parseInt(field.slider_step_value) ?? 5
      } else if (field.type === 'number' || (field.type === 'phone_number' && field.use_simple_text_input)) {
        inputProperties.pattern = '/\d*'
      } else if (field.type === 'phone_number' && !field.use_simple_text_input) {
        inputProperties.unavailableCountries = field.unavailable_countries ?? []
      }

      return inputProperties
    }
  }
}
</script>

<style lang='scss' scoped>
.nf-text {
  ol {
    @apply list-decimal list-inside;
  }

  ul {
    @apply list-disc list-inside;
  }
}
</style>
