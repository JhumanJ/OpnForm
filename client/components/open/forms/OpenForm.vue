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
    <FormTimer
      ref="form-timer"
      :form="form"
    />
    <FormProgressbar
      :form="form"
      :fields="fields"
      :form-data="dataFormValue"
    />
    <transition
      name="fade"
      mode="out-in"
    >
      <div
        :key="formPageIndex"
        class="form-group flex flex-wrap w-full"
      >
        <draggable
          :list="currentFields"
          group="form-elements"
          item-key="id"
          class="grid grid-cols-12 relative transition-all w-full"
          :class="[
            draggingNewBlock ? 'rounded-md bg-blue-50 dark:bg-gray-800' : '',
          ]"
          ghost-class="ghost-item"
          filter=".not-draggable"
          :animation="200"
          :disabled="!formModeStrategy.admin.allowDragging"
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
              :mode="mode"
            />
          </template>
        </draggable>
      </div>
    </transition>

    <!-- Captcha -->
    
    <ClientOnly>
      <div
        v-if="form.use_captcha && isLastPage && hasCaptchaProviders && isCaptchaProviderAvailable"
        class="mb-3 px-2 mt-4 mx-auto w-max"
      >
        <CaptchaInput
          ref="captcha"
          :provider="form.captcha_provider"
          :form="dataForm"
          :language="form.language"
          :dark-mode="darkMode"
        />
      </div>
      <template #fallback>
        <USkeleton class="h-[78px] w-[304px]" />
      </template>
    </ClientOnly>

    <!--  Submit, Next and previous buttons  -->
    <div class="flex flex-wrap justify-center w-full">
      <open-form-button
        v-if="formPageIndex>0 && previousFieldsPageBreak && !loading"
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
        :loading="dataForm.busy"
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
        {{ $t('forms.wrong_form_structure') }}
      </div>
      <div
        v-if="paymentBlock"
        class="mt-6 flex justify-center w-full"
      >
        <p class="text-xs text-gray-400 dark:text-gray-500 flex text-center max-w-md">
          {{ $t('forms.payment.payment_disclaimer') }}
        </p>
      </div>
    </div>
  </form>
</template>

<script>
import draggable from 'vuedraggable'
import OpenFormButton from './OpenFormButton.vue'
import CaptchaInput from '~/components/forms/components/CaptchaInput.vue'
import OpenFormField from './OpenFormField.vue'
import {pendingSubmission} from "~/composables/forms/pendingSubmission.js"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import FormTimer from './FormTimer.vue'
import FormProgressbar from './FormProgressbar.vue'
import { storeToRefs } from 'pinia'
import { FormMode, createFormModeStrategy } from "~/lib/forms/FormModeStrategy.js"
import { FormStructureService } from '~/services/form/services/FormStructureService'
import { FormInitializationService } from '~/services/form/services/FormInitializationService'
import { FormPaymentService } from '~/services/form/services/FormPaymentService'
import { FormValidationService } from '~/services/form/services/FormValidationService'

export default {
  name: 'OpenForm',
  components: {draggable, OpenFormField, OpenFormButton, CaptchaInput, FormTimer, FormProgressbar},
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
    fields: {
      type: Array,
      required: true
    },
    defaultDataForm: { type: [Object, null] },
    mode: {
      type: String,
      default: FormMode.LIVE,
      validator: (value) => Object.values(FormMode).includes(value)
    },
    darkMode: {
      type: Boolean,
      default: false
    }
  },
  emits: ['submit'],
  setup(props) {
    const recordsStore = useRecordsStore()
    const workingFormStore = useWorkingFormStore()
    const dataForm = ref(useForm())
    const config = useRuntimeConfig()
    const route = useRoute()

    // Provide Stripe elements to be used by child components
    const stripeElements = provideStripeElements()
    
    // Initialize FormStructureService
    const formStructure = new FormStructureService(props.form)
    
    // Initialize FormPaymentService
    const formPayment = computed(() => new FormPaymentService(props.form, dataForm.value, stripeElements))

    // Initialize FormValidationService
    const formValidation = computed(() => new FormValidationService(props.form, dataForm.value))

    const hasCaptchaProviders = computed(() => {
      return config.public.hCaptchaSiteKey || config.public.recaptchaSiteKey
    })

    return {
      dataForm,
      recordsStore,
      workingFormStore,
      stripeElements,
      isIframe: useIsIframe(),
      draggingNewBlock: computed(() => workingFormStore.draggingNewBlock),
      pendingSubmission: import.meta.client ? pendingSubmission(props.form) : { get: () => ({}), set: () => {} },
      formPageIndex: storeToRefs(workingFormStore).formPageIndex,
      route,

      // Used for admin previews
      selectedFieldIndex: computed(() => workingFormStore.selectedFieldIndex),
      showEditFieldSidebar: computed(() => workingFormStore.showEditFieldSidebar),
      hasCaptchaProviders,
      formStructure,
      formPayment,
      formValidation
    }
  },

  data() {
    return {
      isAutoSubmit: false,
      isInitialLoad: true,
      // Flag to prevent recursion in field group updates
      isUpdatingFieldGroups: false,
    }
  },

  computed: {
    /**
     * Create field groups (or Page) using page breaks if any
     */
    fieldGroups() {
      return this.formStructure.getFieldGroups()
    },
    /**
     * Gets the comprehensive strategy based on the form mode
     */
    formModeStrategy() {
      return createFormModeStrategy(this.mode)
    },
    /**
     * Determines if hidden fields should be shown based on the mode
     */
    showHidden() {
      return this.formModeStrategy.display.showHiddenFields
    },
    /**
     * Determines if the form is in admin preview mode
     */
    isAdminPreview() {
      return this.formModeStrategy.admin.showAdminControls
    },
    currentFields: {
      get() {
        return this.formStructure.getPageFields(this.formPageIndex)
      },
      set(val) {
        // On re-order from the form, set the new order
        // Add the previous groups and next to val, and set the properties on working form
        const newFields = []
        this.fieldGroups.forEach((group, index) => {
          if (index < this.formPageIndex) {
            newFields.push(...group)
          } else if (index === this.formPageIndex) {
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
      return this.formStructure.getPageBreakBlock(this.formPageIndex)
    },
    previousFieldsPageBreak() {
      return this.formStructure.getPreviousPageBreakBlock(this.formPageIndex)
    },
    /**
     * Returns true if we're on the last page
     * @returns {boolean}xs
     */
    isLastPage() {
      return this.formStructure.isLastPage(this.formPageIndex)
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
        '--form-color': this.form.color
      }
    },
    paymentBlock() {
      return this.formStructure.getPaymentBlock(this.formPageIndex)
    },
    isCaptchaProviderAvailable() {
      const config = useRuntimeConfig()
      if (this.form.captcha_provider === 'recaptcha') {
        return !!config.public.recaptchaSiteKey
      } else if (this.form.captcha_provider === 'hcaptcha') {
        return !!config.public.hCaptchaSiteKey
      }
      return false
    }
  },

  watch: {
    // Monitor only critical changes that require full reinitialization
    'form.database_id': function() {
      // Only reinitialize when database changes
      this.initForm()
    },
    'fields.length': function() {
      // Only reinitialize when fields are added or removed
      this.updateFieldGroupsSafely()
    },
    // Watch for changes to individual field properties
    'fields': {
      deep: true,
      handler() {
        // Skip update if only triggered by internal fieldGroups changes
        if (this.isUpdatingFieldGroups) return
        
        // Safely update field groups without causing recursive updates
        this.updateFieldGroupsSafely()
      }
    },
    dataFormValue: {
      deep: true,
      handler() {
        if (this.isPublicFormPage && this.form && this.form.auto_save) {
          this.pendingSubmission.set(this.dataFormValue)
        }
      }
    },

    // These watchers ensure the form shows the correct page for the field being edited in admin preview
    selectedFieldIndex: {
      handler(newIndex) {
        if (this.isAdminPreview && this.showEditFieldSidebar) {
          this.setPageForField(newIndex)
        }
      }
    },
    showEditFieldSidebar: {
      handler(newValue) {
        if (this.isAdminPreview && newValue) {
          this.setPageForField(this.selectedFieldIndex)
        }
      }
    }
  },
  beforeMount() {
    this.initForm()
  },
  mounted() {
    nextTick(() => {
      if (this.$refs['form-timer']) {
        this.$refs['form-timer'].startTimer()
      }
    })
    if (import.meta.client && window.location.href.includes('auto_submit=true')) {
      this.isAutoSubmit = true
      this.submitForm()
    }
  },

  methods: {
    async submitForm() {
      this.dataForm.busy = true
      
      try {
        if (!await this.nextPage()) {
          this.dataForm.busy = false
          return
        }

        if (!this.isAutoSubmit && this.formPageIndex !== this.fieldGroups.length - 1) {
          this.dataForm.busy = false
          return
        }

        if (this.form.use_captcha && import.meta.client) {
          this.$refs.captcha?.reset()
        }

        if (this.form.editable_submissions && this.form.submission_id) {
          this.dataForm.submission_id = this.form.submission_id
        }

        this.$refs['form-timer']?.stopTimer()
        this.dataForm.completion_time = this.$refs['form-timer']?.completionTime

        // Skip validation if not required by mode strategy
        if (!this.formModeStrategy.validation.validateOnSubmit) {
          this.emitSubmit()
          return
        }

        this.emitSubmit()
      } catch (error) {
        this.formValidation.handleValidationError(error)
        this.dataForm.busy = false
      }
    },

    emitSubmit() {
      const onFailure = () => this.formValidation.onSubmissionFailure(
        this.fieldGroups,
        (index) => this.formPageIndex = index,
        this.$refs['form-timer']
      )
      this.$emit('submit', this.dataForm, onFailure)
    },

    /**
     *   Handle form submission failure
     */
    onSubmissionFailure() {
      this.$refs['form-timer'].startTimer()
      this.isAutoSubmit = false
      this.dataForm.busy = false
      
      if (this.fieldGroups.length > 1) {
        this.showFirstPageWithError()
      }
      this.scrollToFirstError()
    },
    showFirstPageWithError() {
      for (let i = 0; i < this.fieldGroups.length; i++) {
        if (this.fieldGroups[i].some(field => this.dataForm.errors.has(field.id))) {
          this.formPageIndex = i
          break
        }
      }
    },
    scrollToFirstError() {
      if (import.meta.server) return
      const firstErrorElement = document.querySelector('.has-error')
      if (firstErrorElement) {
        window.scroll({
          top: window.scrollY + firstErrorElement.getBoundingClientRect().top - 60,
          behavior: 'smooth'
        })
      }
    },

     /**
     * Form initialization
     */
    async initForm() {
      const formInitService = new FormInitializationService(this.form, this.dataForm, {
        formPageIndex: this.formPageIndex,
        isInitialLoad: this.isInitialLoad
      })
      
      const urlParams = import.meta.client ? new URLSearchParams(window.location.search) : null
      
      await formInitService.initialize({
        defaultData: this.defaultDataForm,
        submissionId: this.route.query?.submission_id,
        urlParams
      })

      this.updateFieldGroupsSafely()
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    handleValidationError(error) {
      this.formValidation.handleValidationError(error)
    },
    isFieldHidden(field) {
      return this.formStructure.isFieldHidden(field, this.dataFormValue)
    },
    getTargetFieldIndex(currentFieldPageIndex) {
      return this.formPageIndex > 0
        ? this.fieldGroups.slice(0, this.formPageIndex).reduce((sum, group) => sum + group.length, 0) + currentFieldPageIndex
        : currentFieldPageIndex
    },
    handleDragDropped(data) {
      if (data.added) {
        const targetIndex = this.formStructure.getTargetFieldIndex(
          data.added.newIndex,
          this.selectedFieldIndex,
          this.formPageIndex
        )
        this.workingFormStore.addBlock(data.added.element, targetIndex, false)
      }
      if (data.moved) {
        const oldTargetIndex = this.formStructure.getTargetFieldIndex(
          data.moved.oldIndex,
          this.selectedFieldIndex,
          this.formPageIndex
        )
        const newTargetIndex = this.formStructure.getTargetFieldIndex(
          data.moved.newIndex,
          this.selectedFieldIndex,
          this.formPageIndex
        )
        this.workingFormStore.moveField(oldTargetIndex, newTargetIndex)
      }
    },
    setPageForField(fieldIndex) {
      if (fieldIndex === -1) return
      this.formPageIndex = this.formStructure.getPageForField(fieldIndex)
    },
    
    // New method for updating field groups
    updateFieldGroups() {
      if (!this.fields || this.fields.length === 0) return

      // Preserve the current page index if possible
      const currentPageIndex = this.formPageIndex
      
      // Use a local variable instead of directly modifying computed property
      // We'll use this to determine totalPages and currentPageIndex
      const calculatedGroups = this.fields.reduce((groups, field, index) => {
        // If the field is a page break, start a new group
        if (field.type === 'nf-page-break' && index !== 0) {
          groups.push([])
        }
        // Add the field to the current group
        if (groups.length === 0) groups.push([])
        groups[groups.length - 1].push(field)
        return groups
      }, [])

      // If we don't have any groups (shouldn't happen), create a default group
      if (calculatedGroups.length === 0) {
        calculatedGroups.push([])
      }

      // Update page navigation
      const totalPages = calculatedGroups.length
      
      // Try to maintain the current page index if valid
      if (currentPageIndex !== undefined && currentPageIndex < totalPages) {
        this.formPageIndex = currentPageIndex
      } else {
        this.formPageIndex = 0
      }

      // Force a re-render of the component, which will update fieldGroups computed property
      this.$forceUpdate()
    },

    // Helper method to prevent recursive updates
    updateFieldGroupsSafely() {
      // Set flag to prevent recursive updates
      this.isUpdatingFieldGroups = true
      
      // Call the actual update method
      this.updateFieldGroups()
      
      // Clear the flag after a short delay to allow Vue to process the update
      this.$nextTick(() => {
        this.isUpdatingFieldGroups = false
      })
    },

    /**
     * Page Navigation
     */
    previousPage() {
      this.formPageIndex--
      this.scrollToTop()
    },

    async nextPage() {
      if (!this.formModeStrategy.validation.validateOnNextPage) {
        if (!this.isLastPage) {
          this.formPageIndex++
        }
        this.scrollToTop()
        return true
      }

      try {
        // Validate current page
        if (!await this.formValidation.validateCurrentPage(this.currentFields, this.formModeStrategy)) {
          return false
        }

        // Process payment if needed using FormPaymentService
        if (!await this.formPayment.processPayment(this.paymentBlock)) {
          return false
        }

        // If validation and payment are successful, proceed
        if (!this.isLastPage) {
          this.formPageIndex++
          this.scrollToTop()
        }

        return true
      } catch (error) {
        this.formValidation.handleValidationError(error)
        return false
      }
    },
  }
}
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md;
}
</style>
