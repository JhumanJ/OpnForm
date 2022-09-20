<template>
  <modal :show="show" @close="close">
    <div v-if="field">
      <div class="flex">
        <h2 class="text-2xl font-bold z-10 truncate mb-5 text-nt-blue flex-grow">
          Configure "<span class="truncate">{{ field.name }}</span>" block
        </h2>
        <div>
          <v-button color="red" size="small" @click="removeBlock">
            Remove Block
          </v-button>
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
          <pro-tag />
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
          <pro-tag />
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
      </div>

      <!-- select/multiselect Options   -->
      <div v-if="['select','multi_select'].includes(field.type)" class="-mx-4 sm:-mx-6 p-5 border-b">
        <h3 class="font-semibold block text-lg">
          Select Options
          <pro-tag />
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
          <pro-tag />
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
        <text-area-input v-else-if="field.type === 'text' && field.multi_lines"
                         name="prefill" class="mt-4"
                         :form="field"
                         label="Pre-filled value"
        />
        <text-input v-else-if="field.type!=='files'" name="prefill" class="mt-4"
                    :form="field"
                    label="Pre-filled value"
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
          <text-input v-model="field.max_char_limit" name="max_char_limit" native-type="number" :min="1" :max="2000" :form="field"
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
          <pro-tag />
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
      <form-block-logic-editor v-model="form" :form="form" :field="field" />

      <div class="pt-5 text-right">
        <v-button color="red" @click="removeBlock">
          Remove Field
        </v-button>
        <v-button color="gray" shade="light" @click="close">
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

import VButton from '../../../common/Button'
import ProTag from '../../../common/ProTag'
import TextInput from '../../../forms/TextInput'
import TextAreaInput from '../../../forms/TextAreaInput'
import timezones from '../../../../../data/timezones.json'
import FormBlockLogicEditor from '../components/form-logic-components/FormBlockLogicEditor'

export default {
  name: 'FormFieldOptionsModal',
  components: { TextAreaInput, TextInput, ProTag, VButton, FormBlockLogicEditor },
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
  data () {
    return {
      typesWithoutPlaceholder: ['date', 'checkbox', 'files']
    }
  },

  computed: {
    hasPlaceholder () {
      return !this.typesWithoutPlaceholder.includes(this.field.type)
    },
    prefillSelectsOptions () {
      if (!['select', 'multi_select'].includes(this.field.type)) return {}

      return this.field[this.field.type].options.map(option => {
        return {
          name: option.name,
          value: option.id
        }
      })
    },
    timezonesOptions () {
      if (this.field.type !== 'date') return []
      return timezones.map((timezone) => {
        return {
          name: timezone.text,
          value: timezone.utc[0]
        }
      })
    },
    displayBasedOnAdvanced () {
      if (this.field.generates_uuid || this.field.generates_auto_increment_id) {
        return false
      }
      return true
    },
    optionsText(){
      return this.field[this.field.type].options.map(option => {
        return option.name
      }).join("\n")
    }
  },

  watch: {},

  mounted () {
    if(['text','number','url','email','phone_number'].includes(this.field.type) && !this.field.max_char_limit){
      this.field.max_char_limit = 2000
    }
  },

  methods: {
    close () {
      this.$emit('close')
    },
    removeBlock () {
      this.close()
      this.$emit('remove-block', this.field)
    },
    onFieldRequiredChange (val) {
      this.$set(this.field, 'required', val)
      if (this.field.required) {
        this.$set(this.field, 'hidden', false)
      }
    },
    onFieldHiddenChange (val) {
      this.$set(this.field, 'hidden', val)
      if (this.field.hidden) {
        this.$set(this.field, 'required', false)
      } else {
        this.$set(this.field, 'generates_uuid', false)
        this.$set(this.field, 'generates_auto_increment_id', false)
      }
    },
    onFieldDateRangeChange (val) {
      this.$set(this.field, 'date_range', val)
      if (this.field.date_range) {
        this.$set(this.field, 'with_time', false)
      }
    },
    onFieldWithTimeChange (val) {
      this.$set(this.field, 'with_time', val)
      if (this.field.with_time) {
        this.$set(this.field, 'date_range', false)
      }
    },
    onFieldGenUIdChange (val) {
      this.$set(this.field, 'generates_uuid', val)
      if (this.field.generates_uuid) {
        this.$set(this.field, 'generates_auto_increment_id', false)
        this.$set(this.field, 'hidden', true)
      }
    },
    onFieldGenAutoIdChange (val) {
      this.$set(this.field, 'generates_auto_increment_id', val)
      if (this.field.generates_auto_increment_id) {
        this.$set(this.field, 'generates_uuid', false)
        this.$set(this.field, 'hidden', true)
      }
    },
    initRating () {
      if (this.field.is_rating && !this.field.rating_max_value) {
        this.$set(this.field, 'rating_max_value', 5)
      }
    },
    onFieldOptionsChange (val) {
      const vals = (val) ? val.trim().split("\n") : []
      const tmpOpts = vals.map(name => {
        return {
          name: name,
          id: name
        }
      })
      this.$set(this.field, this.field.type, {'options': tmpOpts})
    },
    onFieldAllowCreationChange (val) {
      this.$set(this.field, 'allow_creation', val)
      if(this.field.allow_creation){
        this.$set(this.field, 'without_dropdown', false)
      }
    },
    onFieldWithoutDropdownChange (val) {
      this.$set(this.field, 'without_dropdown', val)
      if(this.field.without_dropdown){
        this.$set(this.field, 'allow_creation', false)
      }
    },
  }
}
</script>
