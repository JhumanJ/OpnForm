<template>
  <div
    v-if="field"
    class="py-2"
  >
    <!-- General -->
    <div class="border-b px-4">
      <h3 class="font-semibold block text-lg">
        General
      </h3>
      <p class="text-gray-400 mb-2 text-xs">
        Exclude this field or make it required.
      </p>
      <toggle-switch-input
        :form="field"
        name="required"
        label="Required"
        @update:model-value="onFieldRequiredChange"
      />
      <toggle-switch-input
        :form="field"
        name="hidden"
        label="Hidden"
        @update:model-value="onFieldHiddenChange"
      />
      <toggle-switch-input
        :form="field"
        name="disabled"
        label="Disabled"
        @update:model-value="onFieldDisabledChange"
      />
    </div>

    <!-- Checkbox -->
    <div
      v-if="field.type === 'checkbox'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Checkbox
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for checkbox.
      </p>
      <toggle-switch-input
        :form="field"
        name="use_toggle_switch"
        label="Use toggle switch"
        help="If enabled, checkbox will be replaced with a toggle switch"
      />
    </div>

    <!-- File Uploads -->
    <div
      v-if="field.type === 'files'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg mb-3">
        File uploads
      </h3>
      <toggle-switch-input
        :form="field"
        name="multiple"
        label="Allow multiple files"
      />
      <toggle-switch-input
        :form="field"
        name="camera_upload"
        label="Allow Camera uploads"
      />
      <text-input
        name="allowed_file_types"
        class="mt-3"
        :form="field"
        label="Allowed file types"
        placeholder="jpg,jpeg,png,gif"
        help="Comma separated values, leave blank to allow all file types"
      />

      <text-input
        name="max_file_size"
        class="mt-3"
        :form="field"
        native-type="number"
        :min="1"
        :max="mbLimit"
        label="Maximum file size (in MB)"
        :placeholder="`1MB - ${mbLimit}MB`"
        help="Set the maximum file size that can be uploaded"
      />
    </div>

    <div
      v-if="field.type === 'rating'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Rating
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for rating.
      </p>
      <text-input
        name="rating_max_value"
        native-type="number"
        :min="1"
        class="mt-3"
        :form="field"
        required
        label="Max rating value"
      />
    </div>

    <div
      v-if="field.type === 'scale'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Scale
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for scale.
      </p>
      <text-input
        name="scale_min_value"
        native-type="number"
        class="mt-4"
        :form="field"
        required
        label="Min scale value"
      />
      <text-input
        name="scale_max_value"
        native-type="number"
        :min="1"
        class="mt-4"
        :form="field"
        required
        label="Max scale value"
      />
      <text-input
        name="scale_step_value"
        native-type="number"
        :min="1"
        class="mt-4"
        :form="field"
        required
        label="Scale steps value"
      />
    </div>

    <div
      v-if="field.type === 'slider'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Slider
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for slider.
      </p>
      <text-input
        name="slider_min_value"
        native-type="number"
        class="mt-4"
        :form="field"
        required
        label="Min slider value"
      />
      <text-input
        name="slider_max_value"
        native-type="number"
        :min="1"
        class="mt-4"
        :form="field"
        required
        label="Max slider value"
      />
      <text-input
        name="slider_step_value"
        native-type="number"
        :min="1"
        class="mt-4"
        :form="field"
        required
        label="Slider steps value"
      />
    </div>

    <!--   Text Options   -->
    <div
      v-if="field.type === 'text' && displayBasedOnAdvanced"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Text Options
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Keep it simple or make it a multi-lines input.
      </p>
      <toggle-switch-input
        :form="field"
        name="multi_lines"
        label="Multi-lines input"
        @update:model-value="onFieldMultiLinesChange"
      />
      <toggle-switch-input
        :form="field"
        name="secret_input"
        label="Secret input"
        help="Hide input content with * for privacy"
        @update:model-value="onFieldSecretInputChange"
      />
    </div>

    <!--   Date Options   -->
    <div
      v-if="field.type === 'date'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Date Options
      </h3>
      <toggle-switch-input
        :form="field"
        class="mt-3"
        name="date_range"
        label="End date"
        @update:model-value="onFieldDateRangeChange"
      />
      <toggle-switch-input
        :form="field"
        name="prefill_today"
        label="Prefill with 'today'"
        @update:model-value="onFieldPrefillTodayChange"
      />
      <toggle-switch-input
        :form="field"
        name="disable_past_dates"
        label="Disable past dates"
        @update:model-value="onFieldDisablePastDatesChange"
      />
      <toggle-switch-input
        :form="field"
        name="disable_future_dates"
        label="Disable future dates"
        @update:model-value="onFieldDisableFutureDatesChange"
      />
      <toggle-switch-input
        :form="field"
        name="with_time"
        label="Include time"
      />
      <select-input
        v-if="field.with_time"
        name="timezone"
        class="mt-4"
        :form="field"
        :options="timezonesOptions"
        label="Timezone"
        :searchable="true"
        help="Make sure to select the same timezone you're using in Notion. Leave blank otherwise."
      />
      <flat-select-input
        v-if="field.with_time"
        name="time_format"
        class="mt-4"
        :form="field"
        :options="timeFormatOptions"
        label="Time format"
      />
      <flat-select-input
        name="date_format"
        class="mt-4"
        :form="field"
        :options="dateFormatOptions"
        label="Date format"
      />
    </div>

    <!-- select/multiselect Options   -->
    <div
      v-if="['select', 'multi_select'].includes(field.type)"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Select Options
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for your select/multiselect fields.
      </p>
      <text-area-input
        v-model="optionsText"
        :name="field.id + '_options_text'"
        class="mt-3"
        label="Set selection options"
        help="Add one option per line"
        @update:model-value="onFieldOptionsChange"
      />
      <toggle-switch-input
        :form="field"
        name="allow_creation"
        label="Allow respondent to create new options"
        @update:model-value="onFieldAllowCreationChange"
      />
      <toggle-switch-input
        :form="field"
        name="without_dropdown"
        label="Always show all select options"
        help="Options won't be in a dropdown anymore, but will all be visible"
        @update:model-value="onFieldWithoutDropdownChange"
      />
    </div>

    <!-- Customization - Placeholder, Prefill, Relabel, Field Help    -->
    <div
      v-if="displayBasedOnAdvanced"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg">
        Customization
      </h3>

      <p class="text-gray-400 mb-3 text-xs">
        Change your form field name, pre-fill a value, add hints, etc.
      </p>

      <text-input
        name="name"
        class="mt-3"
        :form="field"
        :required="true"
        label="Field Name"
      />

      <toggle-switch-input
        :form="field"
        name="hide_field_name"
        label="Hide field name"
      />

      <toggle-switch-input
        v-if="field.type === 'phone_number'"
        :form="field"
        name="use_simple_text_input"
        label="Use simple text input"
      />
      <template v-if="field.type === 'phone_number' && !field.use_simple_text_input">
        <select-input
          v-model="field.unavailable_countries"
          class="mt-4"
          wrapper-class="relative"
          :options="allCountries"
          :multiple="true"
          :searchable="true"
          :search-keys="['name']"
          :option-key="'code'"
          :emit-key="'code'"
          label="Disabled countries"
          :placeholder="'Select a country'"
          help="Remove countries from the phone input"
        >
          <template #selected="{ option }">
            <div class="flex items-center space-x-2 justify-center overflow-hidden">
              {{ option.length }} selected
            </div>
          </template>
          <template #option="{ option, selected }">
            <div class="flex items-center space-x-2 hover:text-white">
              <country-flag
                size="normal"
                class="!-mt-[9px]"
                :country="option.code"
              />
              <span class="grow">{{ option.name }}</span>
              <span>{{ option.dial_code }}</span>
            </div>
            <span
              v-if="selected"
              class="absolute inset-y-0 right-0 flex items-center pr-2 dark:text-white"
            >
              <svg
                class="h-5 w-5"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
          </template>
        </select-input>
        <small class="flex -mt-2">
          <a
            href="#"
            class="grow"
            @click.prevent="selectAllCountries"
          >Select All</a>
          <a
            href="#"
            @click.prevent="field.unavailable_countries = null"
          >Un-select All</a>
        </small>
      </template>

      <!-- Pre-fill depends on type -->
      <toggle-switch-input
        v-if="field.type == 'checkbox'"
        :form="field"
        name="prefill"
        label="Pre-filled value"
        @update:model-value="field.prefill = $event"
      />
      <select-input
        v-else-if="['select', 'multi_select'].includes(field.type)"
        name="prefill"
        class="mt-3"
        :form="field"
        :options="prefillSelectsOptions"
        label="Pre-filled value"
        :multiple="field.type === 'multi_select'"
      />
      <date-input
        v-else-if="field.type === 'date' && field.prefill_today !== true"
        name="prefill"
        class="mt-3"
        :form="field"
        :time-format="field.time_format"
        :with-time="field.with_time === true"
        :date-range="field.date_range === true"
        label="Pre-filled value"
      />
      <text-input
        v-else-if="field.type==='date' && field.prefill_today===true"
        name="prefill"
        class="mt-4"
        disabled
        :form="field"
        label="Pre-filled value"
        placeholder="Pre-filled with current date"
      />
      <phone-input
        v-else-if="field.type === 'phone_number' && !field.use_simple_text_input"
        name="prefill"
        class="mt-3"
        :form="field"
        :can-only-country="true"
        :unavailable-countries="field.unavailable_countries ?? []"
        label="Pre-filled value"
      />
      <text-area-input
        v-else-if="field.type === 'text' && field.multi_lines"
        name="prefill"
        class="mt-3"
        :form="field"
        label="Pre-filled value"
      />
      <file-input
        v-else-if="field.type === 'files'"
        name="prefill"
        class="mt-4"
        :form="field"
        label="Pre-filled file"
        :multiple="field.multiple === true"
        :move-to-form-assets="true"
      />
      <text-input
        v-else-if="!['files', 'signature'].includes(field.type)"
        name="prefill"
        class="mt-3"
        :form="field"
        label="Pre-filled value"
      />
      <div
        v-if="['select', 'multi_select'].includes(field.type)"
        class="-mt-3 mb-3 text-gray-400 dark:text-gray-500"
      >
        <small>
          A problem? <a
            href="#"
            @click.prevent="field.prefill = null"
          >Click here to clear your pre-fill</a>
        </small>
      </div>

      <!--    Placeholder    -->
      <text-input
        v-if="hasPlaceholder"
        name="placeholder"
        class="mt-3"
        :form="field"
        label="Empty Input Text (Placeholder)"
      />

      <select-input
        name="width"
        class="mt-3"
        :options="[
          { name: 'Full', value: 'full' },
          { name: '1/2 (half width)', value: '1/2' },
          { name: '1/3 (a third of the width)', value: '1/3' },
          { name: '2/3 (two thirds of the width)', value: '2/3' },
          { name: '1/4 (a quarter of the width)', value: '1/4' },
          { name: '3/4 (three quarters of the width)', value: '3/4' },
        ]"
        :form="field"
        label="Field Width"
      />

      <!--   Help  -->
      <rich-text-area-input
        name="help"
        class="mt-3"
        :form="field"
        :editor-toolbar="editorToolbarCustom"
        label="Field Help"
        :editor-options="{
          formats: [
            'bold',
            'color',
            'font',
            'italic',
            'link',
            'underline',
            'list'
          ]
        }"
        help="Your field help will be shown below/above the field, just like this text."
        :help-position="field.help_position"
      />
      <select-input
        name="help_position"
        class="mt-3"
        :options="[
          { name: 'Below input', value: 'below_input' },
          { name: 'Above input', value: 'above_input' },
        ]"
        :form="field"
        label="Field Help Position"
        @update:model-value="onFieldHelpPositionChange"
      />

      <template v-if="['text', 'number', 'url', 'email'].includes(field.type)">
        <text-input
          name="max_char_limit"
          native-type="number"
          :min="1"
          :max="2000"
          :form="field"
          label="Max character limit"
          help="Maximum character limit of 2000"
          :required="false"
        />
        <checkbox-input
          name="show_char_limit"
          :form="field"
          class="mt-3"
          label="Always show character limit"
        />
      </template>
    </div>

    <!--  Advanced Options   -->
    <div
      v-if="field.type === 'text'"
      class="border-b py-2 px-4"
    >
      <h3 class="font-semibold block text-lg mb-3">
        Advanced Options
      </h3>
      <toggle-switch-input
        :form="field"
        name="generates_uuid"
        label="Generates a unique id"
        help="If you enable this, we will hide this field and fill it with a unique id (UUID format) on each new form submission"
        @update:model-value="onFieldGenUIdChange"
      />
      <toggle-switch-input
        :form="field"
        name="generates_auto_increment_id"
        label="Generates an auto-incremented id"
        help="If you enable this, we will hide this field and fill it a unique incrementing number on each new form submission"
        @update:model-value="onFieldGenAutoIdChange"
      />
    </div>

    <!--  Logic Block -->
    <form-block-logic-editor
      class="py-2 px-4 border-b"
      :form="form"
      :field="field"
    />

    <custom-field-validation 
      class="py-2 px-4 border-b pb-16"
      :form="form"
      :field="field"
    />
  </div>
</template>

<script>
import timezones from '~/data/timezones.json'
import countryCodes from '~/data/country_codes.json'
import CountryFlag from 'vue-country-flag-next'
import FormBlockLogicEditor from '../../components/form-logic-components/FormBlockLogicEditor.vue'
import CustomFieldValidation from '../../components/CustomFieldValidation.vue'
import { format } from 'date-fns'
import { default as _has } from 'lodash/has'

export default {
  name: 'FieldOptions',
  components: { CountryFlag, FormBlockLogicEditor, CustomFieldValidation },
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
  setup() {
    return { currentWorkspace: computed(() => useWorkspacesStore().getCurrent), }
  },
  data() {
    return {
      typesWithoutPlaceholder: ['date', 'checkbox', 'files'],
      editorToolbarCustom: [
        ['bold', 'italic', 'underline', 'link']
      ],
      allCountries: countryCodes
    }
  },

  computed: {
    hasPlaceholder() {
      return !this.typesWithoutPlaceholder.includes(this.field.type)
    },
    mbLimit() {
      return  (this.form?.workspace && this.form?.workspace.max_file_size) ? this.form?.workspace?.max_file_size : 10
    },
    prefillSelectsOptions() {
      if (!['select', 'multi_select'].includes(this.field.type)) return {}

      return this.field[this.field.type].options.map(option => {
        return {
          name: option.name,
          value: option.id
        }
      })
    },
    timezonesOptions() {
      if (this.field.type !== 'date') return []
      return timezones.map((timezone) => {
        return {
          name: timezone.text,
          value: timezone.utc[0]
        }
      })
    },
    dateFormatOptions () {
      const date = new Date()
      return ['dd/MM/yyyy', 'MM-dd-yyyy'].map(dateFormat => {
        return {
          name: format(date, dateFormat),
          value: dateFormat
        }
      })
    },
    timeFormatOptions() {
      return [{ name: '13:00', value: '24', },
      { name: '01:00 PM', value: '12', },]
    },
    displayBasedOnAdvanced() {
      if (this.field.generates_uuid || this.field.generates_auto_increment_id) {
        return false
      }
      return true
    },
    optionsText() {
      if (!this.field[this.field.type]) return ''
      return this.field[this.field.type].options.map(option => {
        return option.name
      }).join('\n')
    }
  },

  watch: {
    'field.width': {
      handler(val) {
        if (val === undefined || val === null) {
          this.field.width = 'full'
        }
      },
      immediate: true
    },
    'field.align': {
      handler(val) {
        if (val === undefined || val === null) {
          this.field.align = 'left'
        }
      },
      immediate: true
    },
    'field.type': {
      handler() {
        this.setDefaultFieldValues()
      },
      immediate: true
    },
  },

  created() {
    if (this.field?.width === undefined || this.field?.width === null) {
      this.field.width = 'full'
    }
  },

  mounted() {
    this.setDefaultFieldValues()
  },

  methods: {
    onFieldDisabledChange(val) {
      this.field.disabled = val
      if (this.field.disabled) {
        this.field.hidden = false
      }
    },
    onFieldRequiredChange(val) {
      this.field.required = val
      if (this.field.required) {
        this.field.hidden = false
      }
    },
    onFieldHiddenChange(val) {
      this.field.hidden = val
      if (this.field.hidden) {
        this.field.required = false
        this.field.disabled = false
      } else {
        this.field.generates_uuid = false
        this.field.generates_auto_increment_id = false
      }
    },
    onFieldDateRangeChange(val) {
      this.field.date_range = val
      if (this.field.date_range) {
        this.field.prefill_today = false
      }
    },
    onFieldGenUIdChange(val) {
      this.field.generates_uuid = val
      if (this.field.generates_uuid) {
        this.field.generates_auto_increment_id = false
        this.field.hidden = true
      }
    },
    onFieldGenAutoIdChange(val) {
      this.field.generates_auto_increment_id = val
      if (this.field.generates_auto_increment_id) {
        this.field.generates_uuid = false
        this.field.hidden = true
      }
    },
    onFieldOptionsChange(val) {
      const vals = (val) ? val.trim().split('\n') : []
      const tmpOpts = vals.map(name => {
        return {
          name: name,
          id: name
        }
      })
      this.field[this.field.type] = { options: tmpOpts }
    },
    onFieldPrefillTodayChange(val) {
      this.field.prefill_today = val
      if (this.field.prefill_today) {
        this.field.prefill = null
        this.field.date_range = false
        this.field.disable_future_dates = false
        this.field.disable_past_dates = false
      } else {
        this.field.prefill = this.field.prefill ?? null
      }
    },
    onFieldAllowCreationChange(val) {
      this.field.allow_creation = val
      if (this.field.allow_creation) {
        this.field.without_dropdown = false
      }
    },
    onFieldWithoutDropdownChange(val) {
      this.field.without_dropdown = val
      if (this.field.without_dropdown) {
        this.field.allow_creation = false
      }
    },
    onFieldDisablePastDatesChange(val) {
      this.field.disable_past_dates = val
      if (this.field.disable_past_dates) {
        this.field.disable_future_dates = false
        this.field.prefill_today = false
      }
    },
    onFieldDisableFutureDatesChange(val) {
      this.field.disable_future_dates = val
      if (this.field.disable_future_dates) {
        this.field.disable_past_dates = false
        this.field.prefill_today = false
      }
    },
    onFieldHelpPositionChange(val) {
      if (!val) {
        this.field.help_position = 'below_input'
      }
    },
    onFieldMultiLinesChange(val) {
      this.field.multi_lines = val
      if (this.field.multi_lines) {
        this.field.secret_input = false
      }
    },
    onFieldSecretInputChange(val) {
      this.field.secret_input = val
      if (this.field.secret_input) {
        this.field.multi_lines = false
      }
    },
    selectAllCountries() {
      this.field.unavailable_countries = this.allCountries.map(item => {
        return item.code
      })
    },
    setDefaultFieldValues() {
      const defaultFieldValues = {
        slider: {
          slider_min_value: 0,
          slider_max_value: 100,
          slider_step_value: 1
        },
        scale: {
          scale_min_value: 1,
          scale_max_value: 5,
          scale_step_value: 1
        },
        rating: {
          rating_max_value: 5
        },
        files: {
          max_file_size: Math.min((this.field.max_file_size ?? this.mbLimit), this.mbLimit)
        },
        text: {
          multi_lines: false,
          max_char_limit: 2000
        },
        email: {
          max_char_limit: 2000
        },
        url: {
          max_char_limit: 2000
        },
        date: {
          date_format: this.dateFormatOptions[0].value,
          time_format: this.timeFormatOptions[0].value
        }
      }
      if (this.field.type in defaultFieldValues) {
        Object.keys(defaultFieldValues[this.field.type]).forEach(key => {
          if (!_has(this.field,key)) {
            this.field[key] = defaultFieldValues[this.field.type][key]
          }
        })
      }
    }
  }
}
</script>
