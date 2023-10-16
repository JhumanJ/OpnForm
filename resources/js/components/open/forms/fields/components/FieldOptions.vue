<template>
  <div v-if="field" class="py-2">
    <!-- General -->
    <div class="border-b px-4">
      <h3 class="font-semibold block text-lg">
        General
      </h3>
      <p class="text-gray-400 mb-2 text-xs">
        Exclude this field or make it required.
      </p>
      <v-checkbox v-model="field.hidden" class="mb-3"
                  :name="field.id+'_hidden'"
                  @input="onFieldHiddenChange"
      >
        Hidden
      </v-checkbox>
      <v-checkbox v-model="field.required" class="mb-3"
                  :name="field.id+'_required'"
                  @input="onFieldRequiredChange"
      >
        Required
      </v-checkbox>
      <v-checkbox v-model="field.disabled" class="mb-3"
                  :name="field.id+'_disabled'"
                  @input="onFieldDisabledChange"
      >
        Disabled
      </v-checkbox>
    </div>

    <!-- Checkbox -->
    <div v-if="field.type === 'checkbox'" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        Checkbox
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for checkbox.
      </p>
      <v-checkbox v-model="field.use_toggle_switch" class="mt-3"
                  name="use_toggle_switch" help=""
      >
        Use toggle switch
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        If enabled, checkbox will be replaced with a toggle switch
      </p>
    </div>

    <!-- File Uploads -->
    <div v-if="field.type === 'files'" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        File uploads
      </h3>
      <v-checkbox v-model="field.multiple" class="mt-3"
                  :name="field.id+'_multiple'"
      >
        Allow multiple files
      </v-checkbox>
      <text-input name="allowed_file_types" class="mt-3" :form="field"
                  label="Allowed file types" placeholder="jpg,jpeg,png,gif"
                  help="Comma separated values, leave blank to allow all file types"
      />
    </div>

    <!--   Number Options   -->
    <div v-if="field.type === 'number'" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        Number Options
      </h3>
      <v-checkbox v-model="field.is_rating" class="mt-3"
                  :name="field.id+'_is_rating'" @input="initRating"
      >
        Rating
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        If enabled then this field will be star rating input.
      </p>

      <text-input v-if="field.is_rating" name="rating_max_value" native-type="number" :min="1" class="mt-3"
                  :form="field" required
                  label="Max rating value"
      />
    </div>

    <!--   Text Options   -->
    <div v-if="field.type === 'text' && displayBasedOnAdvanced" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        Text Options
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Keep it simple or make it a multi-lines input.
      </p>
      <v-checkbox v-model="field.multi_lines" class="mb-2"
                  :name="field.id+'_multi_lines'"
                  @input="$set(field,'multi_lines',$event)"
      >
        Multi-lines input
      </v-checkbox>
    </div>

    <!--   Date Options   -->
    <div v-if="field.type === 'date'" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        Date Options
      </h3>
      <v-checkbox v-model="field.date_range" class="mt-3"
                  :name="field.id+'_date_range'"
                  @input="onFieldDateRangeChange"
      >
        Date Range
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        Adds an end date. This cannot be used with the time option yet.
      </p>
      <v-checkbox v-model="field.with_time"
                  :name="field.id+'_with_time'"
                  @input="onFieldWithTimeChange"
      >
        Date with time
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        Include time. Or not. This cannot be used with the date range option yet.
      </p>

      <select-input v-if="field.with_time" name="timezone" class="mt-3"
                    :form="field" :options="timezonesOptions"
                    label="Timezone" :searchable="true"
                    help="Make sure to select correct timezone. Leave blank otherwise."
      />
      <v-checkbox v-model="field.prefill_today"
                  name="prefill_today"
                  @input="onFieldPrefillTodayChange"
      >
        Prefill with 'today'
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        if enabled we will pre-fill this field with the current date
      </p>

      <v-checkbox v-model="field.disable_past_dates"
                  name="disable_past_dates" class="mb-3"
                  @input="onFieldDisablePastDatesChange"
      >
        Disable past dates
      </v-checkbox>

      <v-checkbox v-model="field.disable_future_dates"
                  name="disable_future_dates" class="mb-3"
                  @input="onFieldDisableFutureDatesChange"
      >
        Disable future dates
      </v-checkbox>
    </div>

    <!-- select/multiselect Options   -->
    <div v-if="['select','multi_select'].includes(field.type)" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        Select Options
      </h3>
      <p class="text-gray-400 mb-3 text-xs">
        Advanced options for your select/multiselect fields.
      </p>
      <text-area-input v-model="optionsText" :name="field.id+'_options_text'" class="mt-3"
                       @input="onFieldOptionsChange"
                       label="Set selection options"
                       help="Add one option per line"
      />
      <v-checkbox v-model="field.allow_creation"
                  name="allow_creation" @input="onFieldAllowCreationChange" help=""
      >
        Allow respondent to create new options
      </v-checkbox>
      <v-checkbox v-model="field.without_dropdown" class="mt-3"
                  name="without_dropdown" @input="onFieldWithoutDropdownChange" help=""
      >
        Always show all select options
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">Options won't be in a dropdown anymore, but will all be visible</p>
    </div>

    <!-- Customization - Placeholder, Prefill, Relabel, Field Help    -->
    <div v-if="displayBasedOnAdvanced" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg">
        Customization
      </h3>

      <p class="text-gray-400 mb-3 text-xs">
        Change your form field name, pre-fill a value, add hints, etc.
      </p>

      <text-input name="name" class="mt-3"
                  :form="field" :required="true"
                  label="Field Name"
      />

      <v-checkbox v-model="field.hide_field_name" class="mt-3"
                  :name="field.id+'_hide_field_name'"
      >
        Hide field name
      </v-checkbox>

      <v-checkbox v-if="field.type === 'phone_number'" v-model="field.use_simple_text_input" class="mt-3"
                  :name="field.id+'_use_simple_text_input'"
      >
        Use simple text input
      </v-checkbox>
      <template v-if="field.type === 'phone_number' && !field.use_simple_text_input">
        <v-select v-model="field.unavailable_countries" class="mt-4"
                  :data="allCountries" :multiple="true"
                  :searchable="true" :search-keys="['name']" :option-key="'code'" :emit-key="'code'"
                  label="Disabled countries" :placeholder="'Select a country'"
                  help="Remove countries from the phone input"
        >
          <template #selected="{option, selected}">
            <div class="flex items-center space-x-2 justify-center overflow-hidden">
              {{ option.length }} selected
            </div>
          </template>
          <template #option="{option, selected}">
            <div class="flex items-center space-x-2 hover:text-white">
              <country-flag size="normal" class="!-mt-[9px]" :country="option.code"/>
              <span class="grow">{{ option.name }}</span>
              <span>{{ option.dial_code }}</span>
            </div>
            <span v-if="selected" class="absolute inset-y-0 right-0 flex items-center pr-2 dark:text-white">
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                />
              </svg>
            </span>
          </template>
        </v-select>
        <small class="flex">
          <a href="#" class="grow" @click.prevent="selectAllCountries">Select All</a>
          <a href="#" @click.prevent="field.unavailable_countries=null">Un-select All</a>
        </small>
      </template>

      <!-- Pre-fill depends on type -->
      <v-checkbox v-if="field.type=='checkbox'" v-model="field.prefill" class="mt-3"
                  :name="field.id+'_prefill'"
                  @input="$set(field,'prefill',$event)"
      >
        Pre-filled value
      </v-checkbox>
      <select-input v-else-if="['select','multi_select'].includes(field.type)" name="prefill" class="mt-3"
                    :form="field" :options="prefillSelectsOptions"
                    label="Pre-filled value"
                    :multiple="field.type==='multi_select'"
      />
      <date-input v-else-if="field.type==='date' && field.prefill_today!==true" name="prefill" class="mt-3"
                  :form="field" :with-time="field.with_time===true"
                  :date-range="field.date_range===true"
                  label="Pre-filled value"
      />
      <phone-input v-else-if="field.type === 'phone_number' && !field.use_simple_text_input"
                   name="prefill" class="mt-3"
                   :form="field" :can-only-country="true" :unavailable-countries="field.unavailable_countries ?? []"
                   label="Pre-filled value"
      />
      <text-area-input v-else-if="field.type === 'text' && field.multi_lines"
                       name="prefill" class="mt-3"
                       :form="field"
                       label="Pre-filled value"
      />
      <file-input v-else-if="field.type==='files'" name="prefill" class="mt-4"
                  :form="field"
                  label="Pre-filled file"
                  :multiple="field.multiple===true" :moveToFormAssets="true"
      />
      <text-input v-else-if="!['files', 'signature'].includes(field.type)" name="prefill" class="mt-3"
                  :form="field"
                  label="Pre-filled value"
                  :disabled="field.type==='date' && field.prefill_today===true"
      />
      <div v-if="['select','multi_select'].includes(field.type)" class="-mt-3 mb-3 text-gray-400 dark:text-gray-500">
        <small>
          A problem? <a href="#" @click.prevent="field.prefill=null">Click here to clear your pre-fill</a>
        </small>
      </div>

      <!--    Placeholder    -->
      <text-input v-if="hasPlaceholder" name="placeholder" class="mt-3"
                  :form="field"
                  label="Empty Input Text (Placeholder)"
      />

      <select-input name="width" class="mt-3"
                    :options="[
                      {name:'Full',value:'full'},
                      {name:'1/2 (half width)',value:'1/2'},
                      {name:'1/3 (a third of the width)',value:'1/3'},
                      {name:'2/3 (two thirds of the width)',value:'2/3'},
                      {name:'1/4 (a quarter of the width)',value:'1/4'},
                      {name:'3/4 (three quarters of the width)',value:'3/4'},
                    ]"
                    :form="field" label="Field Width"
      />

      <!--   Help  -->
      <rich-text-area-input name="help" class="mt-3"
                            :form="field"
                            :editorToolbar="editorToolbarCustom"
                            label="Field Help"
                            help="Your field help will be shown below/above the field, just like this message."
                            :help-position="field.help_position"
      />
      <select-input name="help_position" class="mt-3"
                    :options="[
                    {name:'Below input',value:'below_input'},
                    {name:'Above input',value:'above_input'},
                  ]"
                    :form="field" label="Field Help Position"
                    @input="onFieldHelpPositionChange"
      />

      <template v-if="['text','number','url','email'].includes(field.type)">
        <text-input v-model="field.max_char_limit" name="max_char_limit" native-type="number" :min="1" :max="2000"
                    :form="field"
                    label="Max character limit"
                    help="Maximum character limit of 2000"
                    :required="false"
        />
        <checkbox-input name="show_char_limit" :form="field" class="mt-3"
                        label="Always show character limit"
        />
      </template>

    </div>

    <!--  Advanced Options   -->
    <div v-if="field.type === 'text'" class="border-b py-2 px-4">
      <h3 class="font-semibold block text-lg mb-3">
        Advanced Options
      </h3>

      <v-checkbox v-model="field.generates_uuid"
                  :name="field.id+'_generates_uuid'"
                  @input="onFieldGenUIdChange"
      >
        Generates a unique id
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        If you enable this, we will hide this field and fill it with a unique id (UUID format) on each new form
        submission
      </p>

      <v-checkbox v-model="field.generates_auto_increment_id"
                  :name="field.id+'_generates_auto_increment_id'"
                  @input="onFieldGenAutoIdChange"
      >
        Generates an auto-incremented id
      </v-checkbox>
      <p class="text-gray-400 mb-3 text-xs">
        If you enable this, we will hide this field and fill it a unique incrementing number on each new form submission
      </p>
    </div>

    <!--  Logic Block -->
    <form-block-logic-editor class="py-2 px-4 border-b" v-model="form" :form="form" :field="field"/>
  </div>
</template>

<script>
const FormBlockLogicEditor = () => import('../../components/form-logic-components/FormBlockLogicEditor.vue')
import timezones from '../../../../../../data/timezones.json'
import countryCodes from '../../../../../../data/country_codes.json'
import CountryFlag from 'vue-country-flag'

export default {
  name: 'FieldOptions',
  components: {FormBlockLogicEditor, CountryFlag},
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
  data() {
    return {
      typesWithoutPlaceholder: ['date', 'checkbox', 'files'],
      editorToolbarCustom: [
        ['bold', 'italic', 'underline', 'link'],
      ],
      allCountries: countryCodes,
    }
  },

  computed: {
    hasPlaceholder() {
      return !this.typesWithoutPlaceholder.includes(this.field.type)
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
    displayBasedOnAdvanced() {
      if (this.field.generates_uuid || this.field.generates_auto_increment_id) {
        return false
      }
      return true
    },
    optionsText() {
      return this.field[this.field.type].options.map(option => {
        return option.name
      }).join("\n")
    },
  },

  watch: {
    'field.width': {
      handler(val) {
        if (val === undefined || val === null) {
          this.$set(this.field, 'width', 'full')
        }
      },
      immediate: true
    },
    'field.align': {
      handler(val) {
        if (val === undefined || val === null) {
          this.$set(this.field, 'align', 'left')
        }
      },
      immediate: true
    }
  },

  created() {
    if (this.field?.width === undefined || this.field?.width === null) {
      this.$set(this.field, 'width', 'full')
    }
  },

  mounted() {
    if (['text', 'number', 'url', 'email'].includes(this.field?.type) && !this.field?.max_char_limit) {
      this.field.max_char_limit = 2000
    }
  },

  methods: {
    onFieldDisabledChange(val) {
      this.$set(this.field, 'disabled', val)
      if (this.field.disabled) {
        this.$set(this.field, 'hidden', false)
      }
    },
    onFieldRequiredChange(val) {
      this.$set(this.field, 'required', val)
      if (this.field.required) {
        this.$set(this.field, 'hidden', false)
      }
    },
    onFieldHiddenChange(val) {
      this.$set(this.field, 'hidden', val)
      if (this.field.hidden) {
        this.$set(this.field, 'required', false)
        this.$set(this.field, 'disabled', false)
      } else {
        this.$set(this.field, 'generates_uuid', false)
        this.$set(this.field, 'generates_auto_increment_id', false)
      }
    },
    onFieldDateRangeChange(val) {
      this.$set(this.field, 'date_range', val)
      if (this.field.date_range) {
        this.$set(this.field, 'with_time', false)
        this.$set(this.field, 'prefill_today', false)
      }
    },
    onFieldWithTimeChange(val) {
      this.$set(this.field, 'with_time', val)
      if (this.field.with_time) {
        this.$set(this.field, 'date_range', false)
      }
    },
    onFieldGenUIdChange(val) {
      this.$set(this.field, 'generates_uuid', val)
      if (this.field.generates_uuid) {
        this.$set(this.field, 'generates_auto_increment_id', false)
        this.$set(this.field, 'hidden', true)
      }
    },
    onFieldGenAutoIdChange(val) {
      this.$set(this.field, 'generates_auto_increment_id', val)
      if (this.field.generates_auto_increment_id) {
        this.$set(this.field, 'generates_uuid', false)
        this.$set(this.field, 'hidden', true)
      }
    },
    initRating() {
      if (this.field.is_rating && !this.field.rating_max_value) {
        this.$set(this.field, 'rating_max_value', 5)
      }
    },
    onFieldOptionsChange(val) {
      const vals = (val) ? val.trim().split("\n") : []
      const tmpOpts = vals.map(name => {
        return {
          name: name,
          id: name
        }
      })
      this.$set(this.field, this.field.type, {'options': tmpOpts})
    },
    onFieldPrefillTodayChange(val) {
      this.$set(this.field, 'prefill_today', val)
      if (this.field.prefill_today) {
        this.$set(this.field, 'prefill', 'Pre-filled with current date')
        this.$set(this.field, 'date_range', false)
        this.$set(this.field, 'disable_future_dates', false)
        this.$set(this.field, 'disable_past_dates', false)
      } else {
        this.$set(this.field, 'prefill', null)
      }
    },
    onFieldAllowCreationChange(val) {
      this.$set(this.field, 'allow_creation', val)
      if (this.field.allow_creation) {
        this.$set(this.field, 'without_dropdown', false)
      }
    },
    onFieldWithoutDropdownChange(val) {
      this.$set(this.field, 'without_dropdown', val)
      if (this.field.without_dropdown) {
        this.$set(this.field, 'allow_creation', false)
      }
    },
    onFieldDisablePastDatesChange(val) {
      this.$set(this.field, 'disable_past_dates', val)
      if (this.field.disable_past_dates) {
        this.$set(this.field, 'disable_future_dates', false)
        this.$set(this.field, 'prefill_today', false)
      }
    },
    onFieldDisableFutureDatesChange(val) {
      this.$set(this.field, 'disable_future_dates', val)
      if (this.field.disable_future_dates) {
        this.$set(this.field, 'disable_past_dates', false)
        this.$set(this.field, 'prefill_today', false)
      }
    },
    onFieldHelpPositionChange(val) {
      if (!val) {
        this.$set(this.field, 'help_position', 'below_input')
      }
    },
    selectAllCountries() {
      this.$set(this.field, 'unavailable_countries', this.allCountries.map(item => {
        return item.code
      }))
    }
  }
}
</script>
