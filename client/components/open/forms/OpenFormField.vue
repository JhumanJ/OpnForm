<template>
  <div
    v-if="!isFieldHidden"
    :id="'block-' + field.id"
    ref="form-block"
    class="px-2"
    :class="[
      getFieldWidthClasses(field),
      {
        'group/nffield hover:bg-gray-100/50 relative hover:z-10 transition-colors hover:border-gray-200 dark:hover:!bg-gray-900 border-dashed border border-transparent box-border dark:hover:border-blue-900 rounded-md': isAdminPreview,
        'cursor-pointer':workingFormStore.showEditFieldSidebar && isAdminPreview,
        'bg-blue-50 hover:!bg-blue-50 dark:bg-gray-800 rounded-md': beingEdited,
      }]"
    @click="setFieldAsSelected"
  >
    <div
      class="-m-[1px] w-full max-w-full mx-auto"
      :class="{'relative transition-colors':isAdminPreview}"
    >
      <div
        v-if="isAdminPreview"
        class="absolute translate-y-full lg:translate-y-0 -bottom-1 left-1/2 -translate-x-1/2 lg:-translate-x-full lg:-left-1 lg:top-1 lg:bottom-0 hidden group-hover/nffield:block"
      >
        <div
          class="flex lg:flex-col bg-white !bg-white dark:!bg-white border rounded-md shadow-sm z-50 p-[1px] relative"
        >
          <div
            class="p-1 hover:!text-blue-500 dark:hover:!text-blue-500 hover:bg-blue-50 cursor-pointer !text-gray-500 dark:!text-gray-500 flex items-center justify-center rounded-md"
            role="button"
            @click.prevent="openAddFieldSidebar"
          >
            <UTooltip
              text="Add new field"
              :popper="{ placement: 'right' }"
              class="z-[100]"
            >
              <Icon
                name="i-heroicons-plus-circle-20-solid"
                class="w-5 h-5 !text-gray-500 dark:!text-gray-500 hover:!text-blue-500 dark:hover:!text-blue-500"
              />
            </UTooltip>
          </div>
          <div
            class="p-1 hover:!text-blue-500 dark:hover:!text-blue-500 hover:bg-blue-50 cursor-pointer flex items-center justify-center text-center !text-gray-500 dark:!text-gray-500 rounded-md"
            role="button"
            @click.prevent="editFieldOptions"
          >
            <UTooltip
              text="Edit field settings"
              :popper="{ placement: 'right' }"
              class="z-[100]"
            >
              <Icon
                name="heroicons:cog-8-tooth-20-solid"
                class="w-5 h-5 !text-gray-500 dark:!text-gray-500 hover:!text-blue-500 dark:hover:!text-blue-500"
              />
            </UTooltip>
          </div>
          <div
            class="p-1 hover:!text-red-600 dark:hover:!text-red-600 hover:bg-red-50 cursor-pointer flex items-center justify-center text-center !text-red-500 dark:!text-red-500 rounded-md"
            role="button"
            @click.prevent="removeField"
          >
            <UTooltip
              text="Delete field"
              :popper="{ placement: 'right' }"
              class="z-[100]"
            >
              <Icon
                name="heroicons:trash-20-solid"
                class="w-5 h-5 !text-red-500 dark:!text-red-500 hover:!text-red-600 dark:hover:!text-red-600"
              />
            </UTooltip>
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
          class="nf-text w-full my-1.5"
          :class="[getFieldAlignClasses(field)]"
          v-html="field.content"
        />
        <div
          v-if="field.type === 'nf-code' && field.content"
          :id="field.id"
          :key="field.id"
          class="nf-code w-full px-2 my-1.5"
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
          @dblclick="editFieldOptions"
        >
          <div
            v-if="!field.image_block"
            class="p-4 border border-dashed text-center"
          >
            <a
              href="#"
              class="text-blue-800 dark:text-blue-200"
              @click.prevent="editFieldOptions"
            >Open block settings to upload image.</a>
          </div>
          <img
            v-else
            :alt="field.name"
            :src="field.image_block"
            class="max-w-full inline-block"
            :class="theme.default.borderRadius"
          >
        </div>
      </template>
      <div
        class="hidden group-hover/nffield:flex translate-x-full absolute right-0 top-0 h-full w-5 flex-col justify-center pl-1 pt-3"
      >
        <div
          class="flex items-center bg-gray-100 dark:bg-gray-800 border rounded-md h-12 text-gray-500 dark:text-gray-400 dark:border-gray-500 cursor-grab handle min-h-[40px]"
        >
          <Icon
            name="clarity:drag-handle-line"
            class="h-6 w-6 -ml-1 block shrink-0"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {computed} from 'vue'
import FormLogicPropertyResolver from "~/lib/forms/FormLogicPropertyResolver.js"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import {default as _has} from 'lodash/has'
import { FormMode, createFormModeStrategy } from "~/lib/forms/FormModeStrategy.js"

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
      type: Object, default: () => {
        const theme = inject("theme", null)
        if (theme) {
          return theme.value
        }
        return CachedDefaultTheme.getInstance()
      }
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
    mode: {
      type: String,
      default: FormMode.LIVE
    }
  },

  setup(props) {
    const workingFormStore = useWorkingFormStore()
    return {
      workingFormStore,
      currentWorkspace: computed(() => useWorkspacesStore().getCurrent),
      selectedFieldIndex: computed(() => workingFormStore.selectedFieldIndex),
      showEditFieldSidebar: computed(() => workingFormStore.showEditFieldSidebar),
      formModeStrategy: computed(() => createFormModeStrategy(props.mode)),
      isAdminPreview: computed(() => createFormModeStrategy(props.mode).admin.showAdminControls)
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
        rich_text: 'RichTextAreaInput',
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
        phone_number: 'TextInput',
        matrix: 'MatrixInput',
        barcode: 'BarcodeInput'
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
      return this.isAdminPreview && this.showEditFieldSidebar && this.form.properties.findIndex((item) => {
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
      return this.isAdminPreview && (this.form && this.selectedFieldIndex !== null) ? (this.form.properties[this.selectedFieldIndex] && this.showEditFieldSidebar) : false
    }
  },

  watch: {},

  mounted() {
  },

  methods: {
    editFieldOptions() {
      if (!this.formModeStrategy.admin.showAdminControls) return
      this.workingFormStore.openSettingsForField(this.field)
    },
    setFieldAsSelected () {
      if (!this.formModeStrategy.admin.showAdminControls || !this.workingFormStore.showEditFieldSidebar) return
      this.workingFormStore.openSettingsForField(this.field)
    },
    openAddFieldSidebar() {
      if (!this.formModeStrategy.admin.showAdminControls) return
      this.workingFormStore.openAddFieldSidebar(this.field)
    },
    removeField () {
      if (!this.formModeStrategy.admin.showAdminControls) return
      this.workingFormStore.removeField(this.field)
    },
    getFieldWidthClasses(field) {
      if (!field.width || field.width === 'full') return 'col-span-full'
      else if (field.width === '1/2') {
        return 'sm:col-span-6 col-span-full'
      } else if (field.width === '1/3') {
        return 'sm:col-span-4 col-span-full'
      } else if (field.width === '2/3') {
        return 'sm:col-span-8 col-span-full'
      } else if (field.width === '1/4') {
        return 'sm:col-span-3 col-span-full'
      } else if (field.width === '3/4') {
        return 'sm:col-span-9 col-span-full'
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
        maxCharLimit: (field.max_char_limit) ? parseInt(field.max_char_limit) : null,
        showCharLimit: field.show_char_limit || false,
        isDark: this.darkMode,
        locale: (this.form?.language) ? this.form.language : 'en'
      }

      if (field.type === 'matrix') {
        inputProperties.rows = field.rows
        inputProperties.columns = field.columns
      }

      if (field.type === 'barcode') {
        inputProperties.decoders = field.decoders
      }

      if (['select','multi_select'].includes(field.type) && !this.isFieldRequired) {
        inputProperties.clearable = true
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
        inputProperties.timeFormat = field.time_format
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
        inputProperties.minScale = parseFloat(field.scale_min_value) ?? 1
        inputProperties.maxScale = parseFloat(field.scale_max_value) ?? 5
        inputProperties.stepScale = parseFloat(field.scale_step_value) ?? 1
      } else if (field.type === 'slider') {
        inputProperties.minSlider = parseInt(field.slider_min_value) ?? 0
        inputProperties.maxSlider = parseInt(field.slider_max_value) ?? 50
        inputProperties.stepSlider = parseInt(field.slider_step_value) ?? 5
      } else if (field.type === 'number' || (field.type === 'phone_number' && field.use_simple_text_input)) {
        inputProperties.pattern = '/d*'
      } else if (field.type === 'phone_number' && !field.use_simple_text_input) {
        inputProperties.unavailableCountries = field.unavailable_countries ?? []
      } else if (field.type === 'text' && field.secret_input) {
        inputProperties.nativeType = 'password'
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
