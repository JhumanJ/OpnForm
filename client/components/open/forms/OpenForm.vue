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
      <div v-if="form.use_captcha && isLastPage && hasCaptchaProviders && isCaptchaProviderAvailable" class="mb-3 px-2 mt-4 mx-auto w-max">
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
import { pendingSubmission } from "~/composables/forms/pendingSubmission.js"
import { usePartialSubmission } from "~/composables/forms/usePartialSubmission.js"
import FormLogicPropertyResolver from "~/lib/forms/FormLogicPropertyResolver.js"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import FormTimer from './FormTimer.vue'
import FormProgressbar from './FormProgressbar.vue'
import { storeToRefs } from 'pinia'
import { FormMode, createFormModeStrategy } from "~/lib/forms/FormModeStrategy.js"
import clonedeep from 'clone-deep'
import { provideStripeElements } from '~/composables/useStripeElements'

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

    // Provide Stripe elements to be used by child components
    const stripeElements = provideStripeElements()
    
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
      partialSubmission: import.meta.client ? usePartialSubmission(props.form, dataForm) : { startSync: () => {}, stopSync: () => {} },
      formPageIndex: storeToRefs(workingFormStore).formPageIndex,

      // Used for admin previews
      selectedFieldIndex: computed(() => workingFormStore.selectedFieldIndex),
      showEditFieldSidebar: computed(() => workingFormStore.showEditFieldSidebar),
      hasCaptchaProviders
    }
  },

  data() {
    return {
      isAutoSubmit: false,
      partialSubmissionStarted: false,
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
        return this.fieldGroups[this.formPageIndex]
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
      // Last block from current group
      if (!this.currentFields?.length) return null
      const block = this.currentFields[this.currentFields.length - 1]
      if (block && block.type === 'nf-page-break') return block
      return null
    },
    previousFieldsPageBreak() {
      if (this.formPageIndex === 0) return null
      const previousFields = this.fieldGroups[this.formPageIndex - 1]
      const block = previousFields[previousFields.length - 1]
      if (block && block.type === 'nf-page-break') return block
      return null
    },
    /**
     * Returns true if we're on the last page
     * @returns {boolean}xs
     */
    isLastPage() {
      return this.formPageIndex === (this.fieldGroups.length - 1)
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
      return (this.currentFields) ? this.currentFields.find(field => field.type === 'payment') : null
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
      handler(newValue, oldValue) {
        if (this.isPublicFormPage && this.form && this.form.auto_save) {
          this.pendingSubmission.set(this.dataFormValue)
        }
        // Start partial submission sync on first form change
        if (!this.adminPreview && this.form?.enable_partial_submissions && oldValue && Object.keys(oldValue).length > 0 && !this.partialSubmissionStarted) {
          this.partialSubmission.startSync()
          this.partialSubmissionStarted = true
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
  beforeUnmount() {
    if (!this.adminPreview && this.form?.enable_partial_submissions) {
      this.partialSubmission.stopSync()
    }
  },
  methods: {
    async submitForm() {
      try {
        // Process payment if needed
        if (!await this.doPayment()) {
          return false // Payment failed or was required but not completed
        }
        this.dataForm.busy = false

        // Add submission_id for editable submissions (from main)
        if (this.form.editable_submissions && this.form.submission_id) {
          this.dataForm.submission_id = this.form.submission_id
        }

        // Stop timer and get completion time (from main)
        this.$refs['form-timer'].stopTimer()
        this.dataForm.completion_time = this.$refs['form-timer'].completionTime

        // Add submission hash for partial submissions (from HEAD)
        if (this.form?.enable_partial_submissions) {
          this.dataForm.submission_hash = this.partialSubmission.getSubmissionHash()
        }

        // Add validation strategy check (from main)
        if (!this.formModeStrategy.validation.validateOnSubmit) {
          this.$emit('submit', this.dataForm, this.onSubmissionFailure)
          return
        }

        this.$emit('submit', this.dataForm, this.onSubmissionFailure)
      } catch (error) {
        this.handleValidationError(error)
      } finally {
        this.dataForm.busy = false
      }
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
    async getSubmissionData() {
      if (!this.form || !this.form.editable_submissions || !this.form.submission_id) {
        return null
      }
      await this.recordsStore.loadRecord(
        opnFetch('/forms/' + this.form.slug + '/submissions/' + this.form.submission_id).then((data) => {
          return {submission_id: this.form.submission_id, id: this.form.submission_id, ...data.data}
        }).catch((error) => {
          if (error?.data?.errors) {
            useAlert().formValidationError(error.data)
          } else {
            useAlert().error(error?.data?.message || 'Something went wrong')
          }
          return null
        })
      )
      return this.recordsStore.getByKey(this.form.submission_id)
    },

     /**
     * Form initialization
     */
    async initForm() {
      // Only do a full initialization when necessary
      // Store current page index and form data to avoid overwriting existing values
      const currentFormData = this.dataForm ? clonedeep(this.dataForm.data()) : {}

      // Handle special cases first
      if (this.defaultDataForm) {
        // If we have default data form, initialize with that
        await nextTick(() => {
          this.dataForm.resetAndFill(this.defaultDataForm)
        })
        this.updateFieldGroupsSafely()
        return
      }

      // Initialize the field groups without resetting form data
      this.updateFieldGroupsSafely()

      // Check if we need to handle form submission states
      if (await this.checkForEditableSubmission()) {
        return
      }

      if (this.checkForPendingSubmission()) {
        return
      }

      // Standard initialization with default values
      this.initFormWithDefaultValues(currentFormData)
    },
    
    checkForEditableSubmission() {
      return this.tryInitFormFromEditableSubmission()
    },

    checkForPendingSubmission() {
      if (this.tryInitFormFromPendingSubmission()) {
        this.updateFieldGroupsSafely()
        return true
      }
      return false
    },
    
    async tryInitFormFromEditableSubmission() {
      if (this.isPublicFormPage && this.form.editable_submissions) {
        const submissionId = useRoute().query?.submission_id
        if (submissionId) {
          this.form.submission_id = submissionId
          const data = await this.getSubmissionData()
          if (data) {
            this.dataForm.resetAndFill(data)
            return true
          }
        }
      }
      return false
    },
    
    tryInitFormFromPendingSubmission() {
      if (!this.pendingSubmission || !this.isPublicFormPage || !this.form.auto_save) {
        return false
      }
      
      const pendingData = this.pendingSubmission.get()
      if (pendingData && Object.keys(pendingData).length !== 0) {
        this.updatePendingDataFields(pendingData)
        this.dataForm.resetAndFill(pendingData)
        return true
      }
      
      return false
    },
    
    updatePendingDataFields(pendingData) {
      this.fields.forEach(field => {
        if (field.type === 'date' && field.prefill_today) {
          pendingData[field.id] = new Date().toISOString()
        }
      })
    },
    
    initFormWithDefaultValues(currentFormData = {}) {
      // Only set page 0 on first load, otherwise maintain current position
      if (this.formPageIndex === undefined || this.isInitialLoad) {
        this.formPageIndex = 0
        this.isInitialLoad = false
      }
      
      // Initialize form data with default values
      const formData = { ...currentFormData }
      const urlPrefill = this.isPublicFormPage ? new URLSearchParams(window.location.search) : null

      this.fields.forEach(field => {
        if (field.type.startsWith('nf-') && !['nf-page-body-input', 'nf-page-logo', 'nf-page-cover'].includes(field.type)) return

        this.handleUrlPrefill(field, formData, urlPrefill)
        this.handleDefaultPrefill(field, formData)
      })
      
      // Reset form with new data
      this.dataForm.resetAndFill(formData)
    },
    handleUrlPrefill(field, formData, urlPrefill) {
      if (!urlPrefill) return

      const prefillValue = (() => {
        const val = urlPrefill.get(field.id)
        try {
          return typeof val === 'string' && val.startsWith('{') ? JSON.parse(val) : val
        } catch (e) {
          return val
        }
      })()
      const arrayPrefillValue = urlPrefill.getAll(field.id + '[]')

      if (typeof prefillValue === 'object' && prefillValue !== null) {
        formData[field.id] = { ...prefillValue }
      } else if (prefillValue !== null) {
        formData[field.id] = field.type === 'checkbox' ? this.parseBooleanValue(prefillValue) : prefillValue
      } else if (arrayPrefillValue.length > 0) {
        formData[field.id] = arrayPrefillValue
      }
    },
    parseBooleanValue(value) {
      return value === 'true' || value === '1'
    },
    handleDefaultPrefill(field, formData) {
      if (field.type === 'date' && field.prefill_today) {
        formData[field.id] = new Date().toISOString()
      } else if (field.type === 'matrix' && !formData[field.id]) {
        formData[field.id] = {...field.prefill}
      } else if (!(field.id in formData)) {
        formData[field.id] = field.prefill
      }
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
        this.dataForm.busy = true
        const fieldsToValidate = this.currentFields
          .filter(f => f.type !== 'payment')
          .map(f => f.id)

        // Validate non-payment fields first
        if (fieldsToValidate.length > 0) {
            await this.dataForm.validate('POST', `/forms/${this.form.slug}/answer`, {}, fieldsToValidate)
        }

        // Process payment if needed
        if (!await this.doPayment()) {
          return false // Payment failed or was required but not completed
        }

        // If validation and payment are successful, proceed
        if (!this.isLastPage) {
          this.formPageIndex++
          this.scrollToTop()
        }

        return true
      } catch (error) {
        this.handleValidationError(error)
        return false
      } finally {
        this.dataForm.busy = false
      }
    },
    async doPayment() {
      // Check if there's a payment block in the current step
      if (!this.paymentBlock) {
        return true // No payment needed for this step
      }
      this.dataForm.busy = true

      // Use the stripeElements from setup instead of calling useStripeElements
      const { state: stripeState, processPayment, isCardPopulated, isReadyForPayment } = this.stripeElements
      
      // Skip if payment is already processed in the stripe state
      if (stripeState.intentId) {
        return true
      }
      
      // Skip if payment ID already exists in the form data
      const paymentFieldValue = this.dataFormValue[this.paymentBlock.id]
      if (paymentFieldValue && typeof paymentFieldValue === 'string' && paymentFieldValue.startsWith('pi_')) {
        // If we have a valid payment intent ID in the form data, sync it to the stripe state
        stripeState.intentId = paymentFieldValue
        return true
      }
      
      // Check for the stripe object itself, not just the ready flag
      if (stripeState.isStripeInstanceReady && !stripeState.stripe) {
        stripeState.isStripeInstanceReady = false
      }
      
      // Only process payment if required or card has data
      const shouldProcessPayment = this.paymentBlock.required || isCardPopulated.value
      
      if (shouldProcessPayment) {
        // If not ready yet, try a brief wait
        if (!isReadyForPayment.value) {
          try {
            this.dataForm.busy = true
            
            // Just wait a second to see if state updates (it should be reactive now)
            await new Promise(resolve => setTimeout(resolve, 1000))
            
            // Check if we're ready now
            if (!isReadyForPayment.value) {
              // Provide detailed diagnostics
              let errorMsg = 'Payment system not ready. '
              const details = []
              
              if (!stripeState.stripeAccountId) {
                details.push('No Stripe account connected')
              }
              
              if (!stripeState.isStripeInstanceReady) {
                details.push('Stripe.js not initialized')
              }
              
              if (!stripeState.isCardElementReady) {
                details.push('Card element not initialized')
              }
              
              errorMsg += details.join(', ') + '. Please refresh and try again.'
              useAlert().error(errorMsg)
              return false
            }
          } catch (error) {
            return false
          } finally {
            this.dataForm.busy = false
          }
        }
        
        try {
          this.dataForm.busy = true
          const result = await processPayment(this.form.slug, this.paymentBlock.required)
          
          if (!result.success) {
            // Handle payment error
            if (result.error?.message) {
              this.dataForm.errors.set(this.paymentBlock.id, result.error.message)
              useAlert().error(result.error.message)
            } else {
              useAlert().error('Payment processing failed. Please try again.')
            }
            return false
          }
          
          // Payment successful
          if (result.paymentIntent?.status === 'succeeded') {
            useAlert().success('Thank you! Your payment is successful.')
            return true
          }
          
          // Fallback error
          useAlert().error('Something went wrong with the payment. Please try again.')
          return false
        } catch (error) {
          useAlert().error(error?.message || 'Payment failed')
          return false
        } finally {
          this.dataForm.busy = false
        }
      }
      
      return true // Payment not required or no card data
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    handleValidationError(error) {
      console.error(error)
      if (error?.data) {
        useAlert().formValidationError(error.data)
      }
      this.dataForm.busy = false
    },
    isFieldHidden(field) {
      return (new FormLogicPropertyResolver(field, this.dataFormValue)).isHidden()
    },
    getTargetFieldIndex(currentFieldPageIndex) {
      return this.formPageIndex > 0
        ? this.fieldGroups.slice(0, this.formPageIndex).reduce((sum, group) => sum + group.length, 0) + currentFieldPageIndex
        : currentFieldPageIndex
    },
    handleDragDropped(data) {
      if (data.added) {
        const targetIndex = this.getTargetFieldIndex(data.added.newIndex)
        this.workingFormStore.addBlock(data.added.element, targetIndex, false)
      }
      if (data.moved) {
        const oldTargetIndex = this.getTargetFieldIndex(data.moved.oldIndex)
        const newTargetIndex = this.getTargetFieldIndex(data.moved.newIndex)
        this.workingFormStore.moveField(oldTargetIndex, newTargetIndex)
      }
    },
    setPageForField(fieldIndex) {
      if (fieldIndex === -1) return

      let currentIndex = 0
      for (let i = 0; i < this.fieldGroups.length; i++) {
        currentIndex += this.fieldGroups[i].length
        if (currentIndex > fieldIndex) {
          this.formPageIndex = i
          return
        }
      }
      this.formPageIndex = this.fieldGroups.length - 1
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
    }
  }
}
</script>

<style lang='scss' scoped>
.ghost-item {
  @apply bg-blue-100 dark:bg-blue-900 rounded-md;
}
</style>
