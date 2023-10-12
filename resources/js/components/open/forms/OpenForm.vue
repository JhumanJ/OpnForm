<template>
  <div v-if="isAutoSubmit">
    <p class="text-center p-4">
      <loader class="h-6 w-6 text-nt-blue mx-auto" />
    </p>
  </div>
  <form v-else-if="dataForm" @submit.prevent="">
    <transition name="fade" mode="out-in" appear>
      <template v-for="group, groupIndex in fieldGroups">
        <div v-if="currentFieldGroupIndex===groupIndex"
              :key="groupIndex"
              class="form-group flex flex-wrap w-full">

          <draggable v-model="currentFields"
                     class="flex flex-wrap transition-all"
                     :class="{'-m-6 p-2 bg-gray-50 rounded-md':dragging}"
                     ghost-class="ghost-item"
                     handle=".draggable" :animation="200"
                     @start="onDragStart" @end="onDragEnd"
          >
            <open-form-field v-for="field in group"
                              :key="field.id + formVersionId"
                              :field="field"
                              :show-hidden="showHidden"
                              :form="form"
                              :data-form="dataForm"
                              :data-form-value="dataFormValue"
                              :theme="theme"
                              :admin-preview="adminPreview"
            />
          </draggable>
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
import OpenFormField from './OpenFormField.vue'
import draggable from 'vuedraggable'
const VueHcaptcha = () => import('@hcaptcha/vue-hcaptcha')
import FormPendingSubmissionKey from '../../../mixins/forms/form-pending-submission-key.js'

export default {
  name: 'OpenForm',
  components: {draggable, OpenFormField, OpenFormButton, VueHcaptcha},
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
    },
    adminPreview: { type: Boolean, default: false }, // If used in FormEditorPreview
  },
  data() {
    return {
      dataForm: null,
      currentFieldGroupIndex: 0,
      /**
       * Used to force refresh components by changing their keys
       */
      formVersionId: 1,
      darkModeEnabled: document.body.classList.contains('dark'),
      isAutoSubmit: false,
      /**
       * If currently dragging a field
       */
      dragging: false
    }
  },

  computed: {
    hCaptchaSiteKey: () => window.config.hCaptchaSiteKey,
    /**
     * Create field groups (or Page) using page breaks if any
     */
    fieldGroups() {
      if (!this.fields) return []
      const groups = []
      let currentGroup = []
      this.fields.forEach((field) => {
        if (field.type === 'nf-page-break' && this.isFieldHidden(field)) return
        currentGroup.push(field)
        if (field.type === 'nf-page-break') {
          groups.push(currentGroup)
          currentGroup = []
        }
      })
      groups.push(currentGroup)
      return groups
    },
    currentFields: {
      get () {
        return this.fieldGroups[this.currentFieldGroupIndex]
      },
      set (val) {
        // On re-order from the form, set the new order
        // Add the previous groups and next to val, and set the properties on working form
        const newFields = []
        this.fieldGroups.forEach((group, index) => {
          if (index < this.currentFieldGroupIndex) {
            newFields.push(...group)
          } else if (index === this.currentFieldGroupIndex) {
            newFields.push(...val)
          } else {
            newFields.push(...group)
          }
        })
        // set the properties on working_form vuex
        this.$store.commit('open/working_form/setProperties', newFields)
      }
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
     * @returns {boolean}xs
     */
    isLastPage() {
      return this.currentFieldGroupIndex === (this.fieldGroups.length - 1)
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
        if (this.isPublicFormPage && this.form && this.form.auto_save && this.dataFormValue) {
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

    if (window.location.href.includes('auto_submit=true')) {
      this.isAutoSubmit = true
      this.submitForm()
    }
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
      this.isAutoSubmit = false
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
      if (this.isPublicFormPage && this.form.auto_save) {
        let pendingData
        try {
          pendingData = window.localStorage.getItem(this.formPendingSubmissionKey)
        } catch (e) {
          pendingData = null
        }
        if (pendingData !== null && pendingData) {
          pendingData = JSON.parse(pendingData)
          this.fields.forEach((field) => {
            if (field.type === 'date' && field.prefill_today === true) { // For Prefill with 'today'
              const dateObj = new Date()
              let currentDate = dateObj.getFullYear() + '-' +
                      String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
                      String(dateObj.getDate()).padStart(2, '0')
              if(field.with_time === true){
                currentDate += 'T' + String(dateObj.getHours()).padStart(2, '0') + ':' +
                String(dateObj.getMinutes()).padStart(2, '0');
              }
              pendingData[field.id] = currentDate
            }
          })
          this.dataForm = new Form(pendingData)
          return
        }
      }

      const formData = clonedeep(this.dataForm ? this.dataForm.data() : {})
      let urlPrefill = null
      if (this.isPublicFormPage) {
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
          let currentDate = dateObj.getFullYear() + '-' +
            String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
            String(dateObj.getDate()).padStart(2, '0')
          if(field.with_time === true){
            currentDate += 'T' + String(dateObj.getHours()).padStart(2, '0') + ':' +
            String(dateObj.getMinutes()).padStart(2, '0')
          }
          formData[field.id] = currentDate
        } else { // Default prefill if any
          formData[field.id] = field.prefill
        }

      })
      this.dataForm = new Form(formData)
    },
    previousPage() {
      this.currentFieldGroupIndex -= 1
      window.scrollTo({ top: 0, behavior: 'smooth' })
      return false
    },
    nextPage() {
      this.currentFieldGroupIndex += 1
      window.scrollTo({ top: 0, behavior: 'smooth' })
      return false
    },
    isFieldHidden (field) {
      return (new FormLogicPropertyResolver(field, this.dataFormValue)).isHidden()
    },
    onDragStart () {
      this.dragging = true
    },
    onDragEnd () {
      this.dragging = false
    }
  }
}
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md;
}
</style>
