<template>
  <form v-if="dataForm" @submit.prevent="">
    <transition name="fade" mode="out-in" appear>
      <template v-for="group, groupIndex in fieldGroups">
        <div v-if="currentFieldGroupIndex===groupIndex" :key="groupIndex" class="form-group flex flex-wrap w-full">
          <template v-for="field in group">
            <component :is="getFieldComponents(field)" v-if="getFieldComponents(field)"
                       :key="field.id + formVersionId" :class="getFieldClasses(field)"
                       v-bind="inputProperties(field)" :required="isFieldRequired[field.id]"
            />
            <template v-else>
              <div v-if="field.type === 'nf-text' && field.content" :id="field.id" :key="field.id"
                   class="nf-text w-full px-2 mb-3"
                   v-html="field.content"
              />
              <div v-if="field.type === 'nf-code' && field.content" :id="field.id" :key="field.id"
                   class="nf-code w-full px-2 mb-3"
                   v-html="field.content"
              />
              <div v-if="field.type === 'nf-divider'" :id="field.id" :key="field.id" class="border-b my-4 w-full mx-2"/>
              <div v-if="field.type === 'nf-image' && (field.image_block || !isPublicFormPage)" :id="field.id"
                   :key="field.id" class="my-4 w-full px-2">
                <div v-if="!field.image_block" class="p-4 border border-dashed">
                  Open <b>{{ field.name }}'s</b> block settings to upload image.
                </div>
                <img v-else :alt="field.name" :src="field.image_block" class="max-w-full">
              </div>
            </template>
          </template>
        </div>
      </template>
    </transition>

    <!-- Captcha -->
    <template v-if="form.use_captcha && isLastPage">
      <div class="mb-3 px-2 mt-2 mx-auto w-max">
        <vue-hcaptcha ref="hcaptcha" :sitekey="hCaptchaSiteKey" :theme="darkModeEnabled?'dark':'light'"/>
        <has-error :form="dataForm" field="h-captcha-response"/>
      </div>
    </template>

    <!--  Submit, Next and previous buttons  -->
    <div class="flex flex-wrap justify-center w-full">
      <open-form-button v-if="currentFieldGroupIndex>0 && previousFieldsPageBreak && !loading" native-type="button"
                        :color="form.color" :theme="theme" class="mt-2 px-8 mx-1" @click="previousPage"
      >
        {{ previousFieldsPageBreak.previous_btn_text }}
      </open-form-button>

      <slot v-if="isLastPage" name="submit-btn" :submitForm="submitForm"/>
      <open-form-button v-else native-type="button" :color="form.color" :theme="theme" class="mt-2 px-8 mx-1"
                        @click="nextPage"
      >
        {{ currentFieldsPageBreak.next_btn_text }}
      </open-form-button>
      <div v-if="!currentFieldsPageBreak && !isLastPage">
        Something is wrong with this form structure. If you're the form owner please contact us.
      </div>
    </div>
  </form>
</template>

<script>
import axios from 'axios'
import Form from 'vform'
import OpenFormButton from './OpenFormButton.vue'
import clonedeep from 'clone-deep'
import FormLogicPropertyResolver from '../../../forms/FormLogicPropertyResolver.js'

const VueHcaptcha = () => import('@hcaptcha/vue-hcaptcha')
import FormPendingSubmissionKey from '../../../mixins/forms/form-pending-submission-key.js'

export default {
  name: 'OpenForm',
  components: {OpenFormButton, VueHcaptcha},
  mixins: [FormPendingSubmissionKey],
  props: {
    form: {
      type: Object,
      required: true
    },
    theme: {
      type: Object,
      required: true
    },
    loading: {
      type: Boolean,
      required: true
    },
    showHidden: {
      type: Boolean,
      default: false
    },
    fields: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      dataForm: null,
      currentFieldGroupIndex: 0,

      /**
       * Used to force refresh components by changing their keys
       */
      formVersionId: 1,
      darkModeEnabled: document.body.classList.contains('dark')
    }
  },

  computed: {
    hCaptchaSiteKey: () => window.config.hCaptchaSiteKey,
    actualFields() {
      return this.fields.filter((field) => {
        return this.showHidden || !this.isFieldHidden[field.id]
      })
    },
    /**
     * Create field groups (or Page) using page breaks if any
     */
    fieldGroups() {
      if (!this.actualFields) return []
      const groups = []
      let currentGroup = []
      this.actualFields.forEach((field) => {
        currentGroup.push(field)
        if (field.type === 'nf-page-break') {
          groups.push(currentGroup)
          currentGroup = []
        }
      })
      groups.push(currentGroup)
      return groups
    },
    currentFields() {
      return this.fieldGroups[this.currentFieldGroupIndex]
    },
    /**
     * Returns the page break block for the current group of fields
     */
    currentFieldsPageBreak() {
      const block = this.currentFields[this.currentFields.length - 1]
      if (block && block.type === 'nf-page-break') return block
      return null
    },
    previousFieldsPageBreak() {
      if (this.currentFieldGroupIndex === 0) return null
      const previousFields = this.fieldGroups[this.currentFieldGroupIndex - 1]
      const block = previousFields[previousFields.length - 1]
      if (block && block.type === 'nf-page-break') return block
      return null
    },
    /**
     * Returns true if we're on the last page
     * @returns {boolean}
     */
    isLastPage() {
      return this.currentFieldGroupIndex === (this.fieldGroups.length - 1)
    },
    fieldComponents() {
      return {
        text: 'TextInput',
        number: 'TextInput',
        select: 'SelectInput',
        multi_select: 'SelectInput',
        date: 'DateInput',
        files: 'FileInput',
        checkbox: 'CheckboxInput',
        url: 'TextInput',
        email: 'TextInput',
        phone_number: 'TextInput',
      }
    },
    isPublicFormPage() {
      return this.$route.name === 'forms.show_public'
    },
    dataFormValue() {
      // For get values instead of Id for select/multi select options
      const data = this.dataForm.data()
      const selectionFields = this.fields.filter((field) => {
        return ['select', 'multi_select'].includes(field.type)
      })
      selectionFields.forEach((field) => {
        if (data[field.id] !== undefined && data[field.id] !== null && Array.isArray(data[field.id])) {
          data[field.id] = data[field.id].map(option_nfid => {
            const tmpop = field[field.type].options.find((op) => {
              return (op.id === option_nfid)
            })
            return (tmpop) ? tmpop.name : option_nfid
          })
        }
      })
      return data
    },
    isFieldHidden() {
      const fieldsHidden = {}
      this.fields.forEach((field) => {
        fieldsHidden[field.id] = (new FormLogicPropertyResolver(field, this.dataFormValue)).isHidden()
      })
      return fieldsHidden
    },
    isFieldRequired() {
      const fieldsRequired = {}
      this.fields.forEach((field) => {
        fieldsRequired[field.id] = (new FormLogicPropertyResolver(field, this.dataFormValue)).isRequired()
      })
      return fieldsRequired
    }
  },

  watch: {
    form: {
      deep: true,
      handler() {
        this.initForm()
      }
    },
    fields: {
      deep: true,
      handler() {
        this.initForm()
      }
    },
    theme: {
      handler() {
        this.formVersionId++
      }
    },
    dataForm: {
      deep: true,
      handler() {
        if (this.isPublicFormPage && this.form && this.dataFormValue) {
          try {
            window.localStorage.setItem(this.formPendingSubmissionKey, JSON.stringify(this.dataFormValue))
          } catch (e) {
          }
        }
      }
    },
  },

  mounted() {
    this.initForm()
  },

  methods: {
    submitForm() {
      if (this.currentFieldGroupIndex !== this.fieldGroups.length - 1) {
        return
      }

      if (this.form.use_captcha) {
        this.dataForm['h-captcha-response'] = document.getElementsByName('h-captcha-response')[0].value
        this.$refs.hcaptcha.reset()
      }

      if (this.form.editable_submissions && this.form.submission_id) {
        this.dataForm.submission_id = this.form.submission_id
      }

      this.$emit('submit', this.dataForm, this.onSubmissionFailure)
    },
    /**
     * If more than one page, show first page with error
     */
    onSubmissionFailure() {
      if (this.fieldGroups.length > 1) {
        // Find first mistake and show page
        let pageChanged = false
        this.fieldGroups.forEach((group, groupIndex) => {
          group.forEach((field) => {
            if (pageChanged) return

            if (!pageChanged && this.dataForm.errors.has(field.id)) {
              this.currentFieldGroupIndex = groupIndex
              pageChanged = true
            }
          })
        })
      }

      // Scroll to error
      const elements = document.getElementsByClassName('has-error')
      if (elements.length > 0) {
        window.scroll({
          top: window.scrollY + elements[0].getBoundingClientRect().top - 60,
          behavior: 'smooth'
        })
      }
    },
    async getSubmissionData() {
      if (!this.form || !this.form.editable_submissions || !this.form.submission_id) { return null }
      await this.$store.dispatch('open/records/loadRecord',
        axios.get('/api/forms/' + this.form.slug + '/submissions/' + this.form.submission_id).then((response) => {
          return { submission_id: this.form.submission_id, ...response.data.data }
        })
      )
      return this.$store.getters['open/records/getById'](this.form.submission_id)
    },
    async initForm() {
      if (this.isPublicFormPage && this.form.editable_submissions) {
        const urlParam = new URLSearchParams(window.location.search)
        if (urlParam && urlParam.get('submission_id')) {
          this.form.submission_id = urlParam.get('submission_id')
          const data = await this.getSubmissionData()
          if (data !== null && data) {
            this.dataForm = new Form(data)
            return
          }
        }
      }
      if (this.isPublicFormPage) {
        let pendingData
        try {
          pendingData = window.localStorage.getItem(this.formPendingSubmissionKey)
        } catch (e) {
          pendingData = null
        }
        if (pendingData !== null && pendingData) {
          this.dataForm = new Form(JSON.parse(pendingData))
          return
        }
      }

      const formData = clonedeep(this.dataForm ? this.dataForm.data() : {})
      let urlPrefill = null
      if (this.isPublicFormPage && this.form.is_pro) {
        urlPrefill = new URLSearchParams(window.location.search)
      }

      this.fields.forEach((field) => {
        if (field.type.startsWith('nf-')) {
          return
        }

        if (urlPrefill && urlPrefill.has(field.id)) {
          // Url prefills
          if (field.type === 'checkbox') {
            if (urlPrefill.get(field.id) === 'false' || urlPrefill.get(field.id) === '0') {
              formData[field.id] = false
            } else if (urlPrefill.get(field.id) === 'true' || urlPrefill.get(field.id) === '1') {
              formData[field.id] = true
            }
          } else {
            formData[field.id] = urlPrefill.get(field.id)
          }
        } else if (urlPrefill && urlPrefill.has(field.id + '[]')) {
          // Array url prefills
          formData[field.id] = urlPrefill.getAll(field.id + '[]')
        } else if (field.type === 'date' && field.prefill_today === true) { // For Prefill with 'today'
          const dateObj = new Date()
          const currentDate = dateObj.getFullYear() + '-' +
            String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
            String(dateObj.getDate()).padStart(2, '0')
          formData[field.id] = currentDate
        } else { // Default prefill if any
          formData[field.id] = field.prefill
        }

      })
      this.dataForm = new Form(formData)
    },
    /**
     * Get the right input component for the field/options combination
     */
    getFieldComponents(field) {
      if (field.type === 'text' && field.multi_lines) {
        return 'TextAreaInput'
      }
      if (field.type === 'url' && field.file_upload) {
        return 'FileInput'
      }
      if (field.type === 'number' && field.is_rating && field.rating_max_value) {
        return 'RatingInput'
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
      return this.fieldComponents[field.type]
    },
    getFieldClasses(field) {
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
    /**
     * Get the right input component options for the field/options
     */
    inputProperties(field) {
      const inputProperties = {
        key: field.id,
        name: field.id,
        form: this.dataForm,
        label: (field.hide_field_name) ? null : field.name + (this.isFieldHidden[field.id] ? ' (Hidden Field)' : ''),
        color: this.form.color,
        placeholder: field.placeholder,
        help: field.help,
        uppercaseLabels: this.form.uppercase_labels,
        theme: this.theme,
        maxCharLimit: (field.max_char_limit) ? parseInt(field.max_char_limit) : 2000,
        showCharLimit: field.show_char_limit || false
      }

      if (['select', 'multi_select'].includes(field.type)) {
        inputProperties.options = (field.hasOwnProperty(field.type))
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
        inputProperties.mbLimit = 5
        inputProperties.accept = (this.form.is_pro && field.allowed_file_types) ? field.allowed_file_types : ""
      } else if (field.type === 'number' && field.is_rating) {
        inputProperties.numberOfStars = parseInt(field.rating_max_value)
      }

      return inputProperties
    },
    previousPage() {
      this.currentFieldGroupIndex -= 1
      return false
    },
    nextPage() {
      this.currentFieldGroupIndex += 1
      return false
    }
  }
}
</script>

<style lang="scss">
.nf-text {
  ol {
    @apply list-decimal list-inside;
  }

  ul {
    @apply list-disc list-inside;
  }
}
</style>
