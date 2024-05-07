<template>
  <div
    v-if="!isFieldHidden"
    :id="'block-' + field.id"
    class="border border-transparent box-border hover:bg-gray-50 hover:border-gray-200 dark:hover:bg-gray-900 dark:border-blue-900 rounded-md cursor-grab border-dashed"
    :class="getFieldWidthClasses(field)"
  >
    <div
      class="-m-[1px]"
      :class="getFieldClasses(field)"
    >
      <div
        v-if="adminPreview"
        class="absolute -translate-x-full top-0 bottom-0 opacity-0 group-hover/nffield:opacity-100 transition-opacity mb-4"
      >
        <div
          class="flex flex-col bg-white rounded-md"
          :class="{ 'lg:flex-row': !fieldSideBarOpened, 'xl:flex-row': fieldSideBarOpened }"
        >
          <div
            class="p-2 pr-1 -mb-2 text-gray-300 hover:text-blue-500 cursor-pointer"
            :class="{ 'lg:pr-2': !fieldSideBarOpened, 'xl:pr-2': fieldSideBarOpened }"
            role="button"
            @click.prevent="openAddFieldSidebar"
          >
            <Icon
              name="heroicons:plus-16-solid"
              class="w-6 h-6"
            />
          </div>
          <div
            class="p-2 text-gray-300 hover:text-blue-500 cursor-pointer"
            :class="{ 'lg:pl-0': !fieldSideBarOpened, 'xl:pl-0': fieldSideBarOpened }"
            role="button"
            @click.prevent="editFieldOptions"
          >
            <Icon
              name="heroicons:cog-8-tooth-20-solid"
              class="w-5 h-5"
            />
          </div>
        </div>
      </div>
      <component
        :is="getFieldComponents"
        v-if="getFieldComponents"
        v-bind="inputProperties(field)"
        :required="isFieldRequired"
        :disabled="isFieldDisabled ? true : null"
      />
      <template v-else>
        <div
          v-if="field.type === 'nf-text' && field.content"
          :id="field.id"
          :key="field.id"
          class="nf-text w-full mb-3"
          :class="[getFieldAlignClasses(field)]"
          v-html="field.content"
        />
        <div
          v-if="field.type === 'nf-code' && field.content"
          :id="field.id"
          :key="field.id"
          class="nf-code w-full px-2 mb-3"
          v-html="field.content"
        />
        <div
          v-if="field.type === 'nf-divider'"
          :id="field.id"
          :key="field.id"
          class="border-b my-4 w-full mx-2"
        />
        <div
          v-if="field.type === 'nf-image' && (field.image_block || !isPublicFormPage)"
          :id="field.id"
          :key="field.id"
          class="my-4 w-full px-2"
          :class="[getFieldAlignClasses(field)]"
        >
          <div
            v-if="!field.image_block"
            class="p-4 border border-dashed"
          >
            Open <b>{{ field.name }}'s</b> block settings to upload image.
          </div>
          <img
            v-else
            :alt="field.name"
            :src="field.image_block"
            class="max-w-full"
          >
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
    darkMode: {
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
        showCharLimit: field.show_char_limit || false,
        isDark: this.darkMode
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
        inputProperties.dateFormat = field.date_format
        if (field.with_time) {
          inputProperties.withTime = true
        }
        if (field.date_range) {
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
        inputProperties.pattern = '/d*'
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
