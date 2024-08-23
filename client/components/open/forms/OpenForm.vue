<template>
  <div v-if="isAutoSubmit">
    <p class="text-center p-4">
      <Loader class="h-6 w-6 text-nt-blue mx-auto" />
    </p>
  </div>
  <form
    v-else-if="dataForm"
    :style="computedStyle"
    @submit.prevent=""
  >
    <template v-if="form.show_progress_bar">
      <div
        v-if="isIframe"
        class="mb-4 p-2"
      >
        <div class="w-full h-2 bg-gray-200 dark:bg-gray-600 relative border rounded-full overflow-hidden">
          <div
            class="h-full transition-all duration-300 rounded-r-full"
            :class="{ 'w-0': formProgress === 0 }"
            :style="{ width: formProgress + '%', background: form.color }"
          />
        </div>
      </div>
      <div
        v-else
        class="fixed top-0 left-0 right-0 z-50"
      >
        <div class="w-full h-[0.2rem] bg-gray-200 dark:bg-gray-600 relative overflow-hidden">
          <div
            class="h-full transition-all duration-300"
            :class="{ 'w-0': formProgress === 0 }"
            :style="{ width: formProgress + '%', background: form.color }"
          />
        </div>
      </div>
    </template>
    <transition
      name="fade"
      mode="out-in"
    >
      <div
        :key="currentFieldGroupIndex"
        class="form-group flex flex-wrap w-full"
      >
        <draggable
          :list="currentFields"
          group="form-elements"
          item-key="id"
          class="grid grid-cols-12 relative transition-all w-full"
          :class="{'rounded-md bg-blue-50':draggingNewBlock}"
          ghost-class="ghost-item"
          :animation="200"
          :disabled="!adminPreview"
          handle=".handle"
          @change="handleDragDropped"
        >
          <template #item="{element}">
            <open-form-field
              :field="element"
              :show-hidden="showHidden"
              :form="form"
              :data-form="dataForm"
              :data-form-value="dataFormValue"
              :theme="theme"
              :dark-mode="darkMode"
              :admin-preview="adminPreview"
            />
          </template>
        </draggable>
      </div>
    </transition>

    <!-- Captcha -->
    <template v-if="form.use_captcha && isLastPage">
      <div class="mb-3 px-2 mt-2 mx-auto w-max">
        <vue-hcaptcha
          ref="hcaptcha"
          :sitekey="hCaptchaSiteKey"
          :theme="darkMode?'dark':'light'"
          @opened="setMinHeight(500)"
          @closed="setMinHeight(0)"
        />
        <has-error
          :form="dataForm"
          field="h-captcha-response"
        />
      </div>
    </template>

    <!--  Submit, Next and previous buttons  -->
    <div class="flex flex-wrap justify-center w-full">
      <open-form-button
        v-if="currentFieldGroupIndex>0 && previousFieldsPageBreak && !loading"
        native-type="button"
        :color="form.color"
        :theme="theme"
        class="mt-2 px-8 mx-1"
        @click="previousPage"
      >
        {{ previousFieldsPageBreak.previous_btn_text }}
      </open-form-button>

      <slot
        v-if="isLastPage"
        name="submit-btn"
        :submit-form="submitForm"
      />
      <open-form-button
        v-else-if="currentFieldsPageBreak"
        native-type="button"
        :color="form.color"
        :theme="theme"
        class="mt-2 px-8 mx-1"
        :loading="dataForm.busy"
        @click.stop="nextPage"
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
import clonedeep from 'clone-deep'
import draggable from 'vuedraggable'
import OpenFormButton from './OpenFormButton.vue'
import VueHcaptcha from "@hcaptcha/vue3-hcaptcha"
import OpenFormField from './OpenFormField.vue'
import {pendingSubmission} from "~/composables/forms/pendingSubmission.js"
import FormLogicPropertyResolver from "~/lib/forms/FormLogicPropertyResolver.js"
import {computed} from "vue"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"

export default {
  name: 'OpenForm',
  components: {draggable, OpenFormField, OpenFormButton, VueHcaptcha},
  props: {
    form: {
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
    defaultDataForm: {},
    adminPreview: {type: Boolean, default: false}, // If used in FormEditorPreview
    urlPrefillPreview: {type: Boolean, default: false}, // If used in UrlFormPrefill
    darkMode: {
      type: Boolean,
      default: false
    }
  },

  setup(props) {
    const recordsStore = useRecordsStore()
    const workingFormStore = useWorkingFormStore()
    const dataForm = ref(useForm())

    return {
      dataForm,
      recordsStore,
      workingFormStore,
      isIframe: useIsIframe(),
      draggingNewBlock: computed(() => workingFormStore.draggingNewBlock),
      pendingSubmission: pendingSubmission(props.form)
    }
  },

  data() {
    return {
      currentFieldGroupIndex: 0,
      /**
       * Used to force refresh components by changing their keys
       */
      isAutoSubmit: false,
      minHeight: 0
    }
  },

  computed: {
    hCaptchaSiteKey() {
      return useRuntimeConfig().public.hCaptchaSiteKey
    },
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
    formProgress() {
      const requiredFields = this.fields.filter(field => field.required)
      if (requiredFields.length === 0) {
        return 100
      }
      const completedFields = requiredFields.filter(field => ![null, undefined, ''].includes(this.dataFormValue[field.id]))
      const progress = (completedFields.length / requiredFields.length) * 100
      return Math.round(progress)
    },
    currentFields: {
      get() {
        return this.fieldGroups[this.currentFieldGroupIndex]
      },
      set(val) {
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
        // set the properties on working_form store
        this.workingFormStore.setProperties(newFields)
      }
    },
    /**
     * Returns the page break block for the current group of fields
     */
    currentFieldsPageBreak() {
      // Last block from current group
      if (!this.currentFields?.length) return null
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
      return this.$route.name === 'forms-slug'
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
    computedStyle() {
      return {
        ...this.minHeight ? {minHeight: this.minHeight + 'px'} : {}
      }
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
    dataFormValue: {
      deep: true,
      handler() {
        if (this.isPublicFormPage && this.form && this.form.auto_save) {
          this.pendingSubmission.set(this.dataFormValue)
        }
      }
    }
  },

  mounted() {
    this.initForm()
    if (import.meta.client && window.location.href.includes('auto_submit=true')) {
      this.isAutoSubmit = true
      this.submitForm()
    }
  },

  methods: {
    submitForm() {
      if (this.currentFieldGroupIndex !== this.fieldGroups.length - 1) {
        return
      }

      if (this.form.use_captcha && import.meta.client) {
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
      if (import.meta.server) return
      const elements = document.getElementsByClassName('has-error')
      if (elements.length > 0) {
        window.scroll({
          top: window.scrollY + elements[0].getBoundingClientRect().top - 60,
          behavior: 'smooth'
        })
      }
    },
    async getSubmissionData() {
      if (!this.form || !this.form.editable_submissions || !this.form.submission_id) {
        return null
      }
      await this.recordsStore.loadRecord(
        opnFetch('/forms/' + this.form.slug + '/submissions/' + this.form.submission_id).then((data) => {
          return {submission_id: this.form.submission_id, id: this.form.submission_id, ...data.data}
        })
      )
      return this.recordsStore.getByKey(this.form.submission_id)
    },
    async initForm() {
      if (this.defaultDataForm) {
        this.dataForm = useForm(this.defaultDataForm)
        return
      }

      if (this.isPublicFormPage && this.form.editable_submissions) {
        if (useRoute().query?.submission_id) {
          this.form.submission_id = useRoute().query?.submission_id
          const data = await this.getSubmissionData()
          if (data !== null && data) {
            this.dataForm = useForm(data)
            return
          }
        }
      }
      if (this.isPublicFormPage && this.form.auto_save) {
        const pendingData = this.pendingSubmission.get()
        if (pendingData !== null && pendingData && Object.keys(this.pendingSubmission.get()).length !== 0) {
          this.fields.forEach((field) => {
            if (field.type === 'date' && field.prefill_today === true) { // For Prefill with 'today'
              pendingData[field.id] = new Date().toISOString()
            }
          })
          this.dataForm = useForm(pendingData)
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
          formData[field.id] = new Date().toISOString()
        } else { // Default prefill if any
          formData[field.id] = field.prefill
        }
      })
      this.dataForm = useForm(formData)
    },
    previousPage() {
      this.currentFieldGroupIndex -= 1
      window.scrollTo({top: 0, behavior: 'smooth'})
      return false
    },
    nextPage() {
      if (this.adminPreview || this.urlPrefillPreview) {
        this.currentFieldGroupIndex += 1
        window.scrollTo({top: 0, behavior: 'smooth'})
        return false
      }
      const fieldsToValidate = this.currentFields.map(f => f.id)
      this.dataForm.busy = true
      this.dataForm.validate('POST', '/forms/' + this.form.slug + '/answer', {}, fieldsToValidate)
        .then((data) => {
          this.currentFieldGroupIndex += 1
          this.dataForm.busy = false
          window.scrollTo({top: 0, behavior: 'smooth'})
        }).catch(error => {
        console.error(error)
        if (error && error.data && error.data.message) {
          useAlert().error(error.data.message)
        }
        this.dataForm.busy = false
      })
      return false
    },
    isFieldHidden(field) {
      return (new FormLogicPropertyResolver(field, this.dataFormValue)).isHidden()
    },
    getTargetFieldIndex(currentFieldPageIndex) {
      let targetIndex = 0
      if (this.currentFieldGroupIndex > 0) {
        for (let i = 0; i < this.currentFieldGroupIndex; i++) {
          targetIndex += this.fieldGroups[i].length
        }
        targetIndex += currentFieldPageIndex
      } else {
        targetIndex = currentFieldPageIndex
      }
      return targetIndex
    },
    handleDragDropped(data) {
      if (data.added) {
        const targetIndex = this.getTargetFieldIndex(data.added.newIndex)
        this.workingFormStore.addBlock(data.added.element, targetIndex)
      }

      if (data.moved) {
        const oldTargetIndex = this.getTargetFieldIndex(data.moved.oldIndex)
        const newTargetIndex = this.getTargetFieldIndex(data.moved.newIndex)
        this.workingFormStore.moveField(oldTargetIndex, newTargetIndex)
      }
    },
    setMinHeight(minHeight) {
      if (!this.isIframe) {
        return
      }
      this.minHeight = minHeight
      // Trigger window iframe resize
      try {
        window.parentIFrame.size()
      } catch (e) {
        console.error(e)
      }
    }
  }
}
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md;
}
</style>
