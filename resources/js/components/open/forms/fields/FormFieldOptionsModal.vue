<template>
  <modal :show="show" @close="close">
    <div v-if="field">
      <div class="flex">
        <h2 class="text-2xl font-semibold z-10 truncate mb-5 text-gray-900 flex-grow">
          Configure "<span class="truncate">{{ field.name }}</span>" block
        </h2>
        <div class="flex flex-wrap justify-end mr-6">
          <div>
            <v-button color="light-gray" size="small" @click="removeBlock">
              <svg class="h-4 w-4 text-red-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M3 6H5M5 6H21M5 6V20C5 20.5304 5.21071 21.0391 5.58579 21.4142C5.96086 21.7893 6.46957 22 7 22H17C17.5304 22 18.0391 21.7893 18.4142 21.4142C18.7893 21.0391 19 20.5304 19 20V6H5ZM8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M10 11V17M14 11V17"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>

              Remove
            </v-button>
          </div>
          <div class="ml-1">
            <v-button size="small" color="light-gray" @click="duplicateBlock">
              <svg class="h-4 w-4 text-blue-600 inline mr-1 -mt-1" viewBox="0 0 24 24" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M5 15H4C3.46957 15 2.96086 14.7893 2.58579 14.4142C2.21071 14.0391 2 13.5304 2 13V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H13C13.5304 2 14.0391 2.21071 14.4142 2.58579C14.7893 2.96086 15 3.46957 15 4V5M11 9H20C21.1046 9 22 9.89543 22 11V20C22 21.1046 21.1046 22 20 22H11C9.89543 22 9 21.1046 9 20V11C9 9.89543 9.89543 9 11 9Z"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Duplicate
            </v-button>
          </div>
          <change-field-type class="my-1" :field="field" @changeType="onChangeType"/>
        </div>
      </div>

      <!-- General -->
      <div class="-mx-4 sm:-mx-6 p-5 border-b border-t">
        <h3 class="font-semibold block text-lg">
          General
        </h3>
        <p class="text-gray-400 mb-5">
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
      </div>

      <!-- Checkbox -->
      <div v-if="field.type === 'checkbox'" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Checkbox
        </h3>
        <p class="text-gray-400 mb-5">
          Advanced options for checkbox.
        </p>
        <v-checkbox v-model="field.use_toggle_switch" class="mt-4"
                    name="use_toggle_switch" help=""
        >
          Use toggle switch
        </v-checkbox>
        <p class="text-gray-400 mb-5">
          If enabled, checkbox will be replaced with a toggle switch
        </p>
      </div>

      <!-- File Uploads -->
      <div v-if="field.type === 'files'" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          File uploads
        </h3>
        <v-checkbox v-model="field.multiple" class="mt-4"
                    :name="field.id+'_multiple'"
        >
          Allow multiple files
        </v-checkbox>
        <text-input name="allowed_file_types" class="mt-4" :form="field"
                    label="Allowed file types" placeholder="jpg,jpeg,png,gif"
                    help="Comma separated values, leave blank to allow all file types"
        />
      </div>

      <!--   Number Options   -->
      <div v-if="field.type === 'number'" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Number Options
          <pro-tag/>
        </h3>
        <v-checkbox v-model="field.is_rating" class="mt-4"
                    :name="field.id+'_is_rating'" @input="initRating"
        >
          Rating
        </v-checkbox>
        <p class="text-gray-400 mb-5">
          If enabled then this field will be star rating input.
        </p>

        <text-input v-if="field.is_rating" name="rating_max_value" native-type="number" :min="1" class="mt-4"
                    :form="field" required
                    label="Max rating value"
        />
      </div>

      <!--   Text Options   -->
      <div v-if="field.type === 'text' && displayBasedOnAdvanced" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Text Options
        </h3>
        <p class="text-gray-400 mb-5">
          Keep it simple or make it a multi-lines input.
        </p>
        <v-checkbox v-model="field.multi_lines"
                    :name="field.id+'_multi_lines'"
                    @input="$set(field,'multi_lines',$event)"
        >
          Multi-lines input
        </v-checkbox>
      </div>

      <!--   Date Options   -->
      <div v-if="field.type === 'date'" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Date Options
          <pro-tag/>
        </h3>
        <v-checkbox v-model="field.date_range" class="mt-4"
                    :name="field.id+'_date_range'"
                    @input="onFieldDateRangeChange"
        >
          Date Range
        </v-checkbox>
        <p class="text-gray-400 mb-5">
          Adds an end date. This cannot be used with the time option yet.
        </p>
        <v-checkbox v-model="field.with_time"
                    :name="field.id+'_with_time'"
                    @input="onFieldWithTimeChange"
        >
          Date with time
        </v-checkbox>
        <p class="text-gray-400 mb-5">
          Include time. Or not. This cannot be used with the date range option yet.
        </p>

        <select-input v-if="field.with_time" name="timezone" class="mt-4"
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
        <p class="text-gray-400 mb-5">
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
      <div v-if="['select','multi_select'].includes(field.type)" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Select Options
          <pro-tag/>
        </h3>
        <p class="text-gray-400 mb-5">
          Advanced options for your select/multiselect fields.
        </p>
        <text-area-input v-model="optionsText" :name="field.id+'_options_text'" class="mt-4"
                         @input="onFieldOptionsChange"
                         label="Set selection options"
                         help="Add one option per line"
        />
        <v-checkbox v-model="field.allow_creation"
                    name="allow_creation" @input="onFieldAllowCreationChange" help=""
        >
          Allow respondent to create new options
        </v-checkbox>
        <v-checkbox v-model="field.without_dropdown" class="mt-4"
                    name="without_dropdown" @input="onFieldWithoutDropdownChange" help=""
        >
          Always show all select options
        </v-checkbox>
        <p class="text-gray-400 mb-5">Options won't be in a dropdown anymore, but will all be visible</p>
      </div>

      <!-- Customization - Placeholder, Prefill, Relabel, Field Help    -->
      <div v-if="displayBasedOnAdvanced" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Customization
          <pro-tag/>
        </h3>

        <p class="text-gray-400 mb-5">
          Change your form field name, pre-fill a value, add hints.
        </p>

        <text-input name="name" class="mt-4"
                    :form="field" :required="true"
                    label="Field Name"
        />

        <v-checkbox v-model="field.hide_field_name" class="mb-3"
                    :name="field.id+'_hide_field_name'"
        >
          Hide field name
        </v-checkbox>

        <!-- Pre-fill depends on type -->
        <v-checkbox v-if="field.type=='checkbox'" v-model="field.prefill" class="mt-4"
                    :name="field.id+'_prefill'"
                    @input="$set(field,'prefill',$event)"
        >
          Pre-filled value
        </v-checkbox>
        <select-input v-else-if="['select','multi_select'].includes(field.type)" name="prefill" class="mt-4"
                      :form="field" :options="prefillSelectsOptions"
                      label="Pre-filled value"
                      :multiple="field.type==='multi_select'"
        />
        <date-input v-else-if="field.type==='date' && field.prefill_today!==true" name="prefill" class="mt-4"
                    :form="field" :with-time="field.with_time===true"
                    :date-range="field.date_range===true"
                    label="Pre-filled value"
        />
        <text-area-input v-else-if="field.type === 'text' && field.multi_lines"
                         name="prefill" class="mt-4"
                         :form="field"
                         label="Pre-filled value"
        />
        <text-input v-else-if="field.type!=='files'" name="prefill" class="mt-4"
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
        <text-input v-if="hasPlaceholder" name="placeholder" class="mt-4"
                    :form="field"
                    label="Empty Input Text (Placeholder)"
        />

        <!--   Help  -->
        <text-input name="help" class="mt-4"
                    :form="field"
                    label="Field Help"

                    help="Your field help will be shown below the field, just like this message."
        />

        <select-input name="width" class="mt-4"
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

        <template v-if="['text','number','url','email','phone_number'].includes(field.type)">
          <text-input v-model="field.max_char_limit" name="max_char_limit" native-type="number" :min="1" :max="2000"
                      :form="field"
                      label="Max character limit"
                      help="Maximum character limit of 2000"
                      :required="false"
          />
          <checkbox-input name="show_char_limit" :form="field" class="mt-4"
                          label="Always show character limit"
          />
        </template>

      </div>

      <!--  Advanced Options   -->
      <div v-if="field.type === 'text'" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Advanced Options
          <pro-tag/>
        </h3>

        <v-checkbox v-model="field.generates_uuid"
                    :name="field.id+'_generates_uuid'"
                    @input="onFieldGenUIdChange"
        >
          Generates a unique id on submission
        </v-checkbox>
        <p class="text-gray-400 mb-5">
          If you enable this, we will hide this field and fill it a unique id (UUID format) on each new form submission
        </p>

        <v-checkbox v-model="field.generates_auto_increment_id"
                    :name="field.id+'_generates_auto_increment_id'"
                    @input="onFieldGenAutoIdChange"
        >
          Generates an auto-incremented id on submission
        </v-checkbox>
        <p class="text-gray-400 mb-5">
          If you enable this, we will hide this field and fill it a unique number on each new form submission
        </p>
      </div>

      <!--  Logic Block -->
      <form-block-logic-editor v-model="form" :form="form" :field="field"/>

      <div class="pt-5 flex justify-end">
        <v-button color="white" @click="close">
          Close
        </v-button>
      </div>
    </div>
    <div v-else class="text-center p-10">
      Field not found.
    </div>
  </modal>
</template>

<script>

import timezones from '../../../../../data/timezones.json'
import ProTag from "../../../common/ProTag.vue"

const FormBlockLogicEditor = () => import('../components/form-logic-components/FormBlockLogicEditor.vue')
import ChangeFieldType from "./components/ChangeFieldType.vue"

export default {
  name: 'FormFieldOptionsModal',
  components: {ProTag, FormBlockLogicEditor, ChangeFieldType},
  props: {
    field: {
      type: Object,
      required: false
    },
    form: {
      type: Object,
      required: false
    },
    show: {
      type: Boolean,
      required: false
    }
  },
  data() {
    return {
      typesWithoutPlaceholder: ['date', 'checkbox', 'files']
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

  watch: {},

  mounted() {
    if (['text', 'number', 'url', 'email', 'phone_number'].includes(this.field.type) && !this.field.max_char_limit) {
      this.field.max_char_limit = 2000
    }
  },

  methods: {
    onChangeType(newType) {
      if(['select', 'multi_select'].includes(this.field.type)){
        this.$set(this.field, newType, this.field[this.field.type]) // Set new options with new type
        this.$delete(this.field, this.field.type) // remove old type options
      }
      this.$set(this.field, 'type', newType)
    },
    close() {
      this.$emit('close')
    },
    removeBlock() {
      this.close()
      this.$emit('remove-block', this.field)
    },
    duplicateBlock() {
      this.close()
      this.$emit('duplicate-block', this.field)
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
    }
  }
}
</script>
