import { reactive, computed, ref, toValue, onBeforeUnmount } from 'vue'
import { useForm } from '~/composables/useForm.js' // Assuming useForm handles vForm setup
import { FormMode, createFormModeStrategy } from '../FormModeStrategy'
import { useFormStructure } from './useFormStructure'
import { useFormInitialization } from './useFormInitialization'
import { useFormValidation } from './useFormValidation'
import { useFormSubmission } from './useFormSubmission'
import { useFormPayment } from './useFormPayment'
import { useFormTimer } from './useFormTimer'
import { usePendingSubmission } from '~/lib/forms/composables/usePendingSubmission.js'
import { usePartialSubmission } from '~/composables/forms/usePartialSubmission.js'
import { useIsIframe } from '~/composables/useIsIframe'
import { useAmplitude } from '~/composables/useAmplitude'
import { useConfetti } from '~/composables/useConfetti'
import { cloneDeep } from 'lodash'

/**
 * @fileoverview Main orchestrator composable for form operations.
 * Initializes and coordinates various form composables (Structure, Init, Validation, etc.)
 * based on the provided form configuration and mode.
 */
export function useFormManager(initialFormConfig, mode = FormMode.LIVE, options = {}) {
  // --- Reactive State ---
  const config = ref(initialFormConfig) // Use ref for potentially replaceable config
  const form = useForm() // Core vForm instance
  const strategy = computed(() => createFormModeStrategy(mode)) // Strategy based on mode
  
  // Use the passed darkMode ref if it's a ref, otherwise create a new ref
  const darkMode = options.darkMode && typeof options.darkMode === 'object' && 'value' in options.darkMode 
    ? options.darkMode 
    : ref(options.darkMode || false)

  const state = reactive({
    currentPage: 0,
    isSubmitted: false,
    isProcessing: false, // Unified flag for async ops
  })

  // --- Initialize services that depend on config and form data ---
  // Create a reactive reference to the form data for dependent composables to watch
  const formDataRef = computed(() => form.data())

  // Instantiate pending submission service (handles localStorage saving)
  const pendingSubmissionService = usePendingSubmission(config, formDataRef)
  
  // Instantiate partial submission service (handles server auto-sync)
  const partialSubmissionService = usePartialSubmission(config, formDataRef, pendingSubmissionService)

  // --- Instantiate Other Composables (Services) ---
  const timer = useFormTimer(pendingSubmissionService)
  const initialization = useFormInitialization(config, form, pendingSubmissionService)
  const structure = useFormStructure(config, state, form) 
  const validation = useFormValidation(config, form, state)
  const payment = useFormPayment(config, form)
  const submission = useFormSubmission(config, form)

  /**
   * Initializes the form: loads data, resets state, starts timer.
   * @param {Object} options - Initialization options (passed to useFormInitialization).
   */
  const initialize = async (options = {}) => {
    state.isProcessing = true
    state.isSubmitted = false
    state.currentPage = 0
   
    await initialization.initialize({
      ...options
    })

    timer.reset()
    timer.start()
    
    // Start partial submission sync if enabled
    if (import.meta.client && config.value.enable_partial_submissions) {
      partialSubmissionService.startSync()
    }
    
    state.isProcessing = false
  }

  /**
   * Navigates to the next page, handling validation and payment intent creation.
   */
  const nextPage = async () => {
    if (state.isProcessing) return false
    state.isProcessing = true

    try {
      const currentPageFields = structure.getPageFields(state.currentPage)
      // Use computed isLastPage directly from structure composable
      const isCurrentlyLastPage = structure.isLastPage.value 

      // 1. Validate current page
      await validation.validateCurrentPage(currentPageFields, strategy.value)

      // 2. Process payment (Create Payment Intent if applicable)
      const paymentBlock = structure.currentPagePaymentBlock.value
      if (paymentBlock) {
        // In editor/test mode (not LIVE), skip payment validation
        const isPaymentRequired = mode === FormMode.LIVE ? !!paymentBlock.required : false
        
        // Pass required refs if Stripe needs them now (unlikely for just intent creation)
        const paymentResult = await payment.processPayment(paymentBlock, isPaymentRequired)
        if (!paymentResult.success) {
          throw paymentResult.error || new Error('Payment intent creation failed')
        }
      }

      // 3. Move to the next page if not the last
      if (!isCurrentlyLastPage) {
        state.currentPage++
      }
      state.isProcessing = false
      return true

    } catch {
      // Use validation composable's failure handler
      validation.onValidationFailure({
        fieldGroups: structure.fieldGroups.value, // Pass reactive groups
        setPageIndexCallback: (index) => { state.currentPage = index },
        timerService: timer // Pass the timer composable instance
      })
      state.isProcessing = false
      return false
    }
  }

  /** Navigates to the previous page */
  const previousPage = () => {
    if (state.currentPage > 0 && !state.isProcessing) {
      state.currentPage--
    }
  }

  /**
   * Attempts the final form submission.
   * Handles timer stop, final validation, captcha, and submission call.
   * @param {Object} submitOptions - Optional extra data for submission.
   * @returns {Promise<Object>} Result from submission composable.
   */
  const submit = async (submitOptions = {}) => {
    if (state.isProcessing) {
        return Promise.reject('Processing')
    }
    state.isProcessing = true

    try {
      // Stop partial submission sync during submission if enabled
      if (!import.meta.server && toValue(config).enable_partial_submissions) {
        partialSubmissionService.stopSync() // This will sync immediately before stopping
      }
      
      // 1. Stop Timer & Get Time
      timer.stop()
      const completionTime = timer.getCompletionTime()

      // 2. Process payment if applicable
      const paymentBlock = structure.currentPagePaymentBlock.value
      if (paymentBlock) {
        
        // In editor/test mode (not LIVE), skip payment validation
        const isPaymentRequired = mode === FormMode.LIVE ? !!paymentBlock.required : false
        
        const paymentResult = await payment.processPayment(paymentBlock, isPaymentRequired)
        
        if (!paymentResult.success) {
          // If payment was skipped because it's not required, we continue
          if (!paymentResult.skipped) {
            // Payment error - don't proceed with submission
            state.isProcessing = false
            throw new Error(paymentResult.error || 'Payment failed')
          }
        }
      }
      
      // 3. Get submission hash from partialSubmission if enabled
      let submissionHash = null
      if (!import.meta.server && toValue(config).enable_partial_submissions) {
        submissionHash = partialSubmissionService.getSubmissionHash()
      }

      // 4. Perform Submission (using submission composable)
      const submissionResult = await submission.submit({
        formModeStrategy: strategy.value,
        completionTime: completionTime,
        submissionHash: submissionHash,
        ...submitOptions
      })

      // 5. Update State on Success
      state.isSubmitted = true
      state.isProcessing = false
      
      // 6. Play confetti if enabled in config
      if (import.meta.client && toValue(config).confetti_on_submission) {
        useConfetti().play()
      }
      
      // 7. Clear pending submission data on successful submit
      pendingSubmissionService?.clear()
      
      // 8. Handle amplitude logging
      if (import.meta.client) {
        const amplitude = useAmplitude()
        amplitude.logEvent('form_submission', {
          workspace_id: toValue(config).workspace_id,
          form_id: toValue(config).id
        })
      }
      
      // 9. Handle postMessage communication for iframe integration
      if (import.meta.client) {
        const isIframe = useIsIframe()
        const formConfig = toValue(config)
        const payload = cloneDeep({
          type: 'form-submitted',
          form: {
            slug: formConfig.slug,
            id: formConfig.id,
            redirect_target_url: (formConfig.is_pro && submissionResult?.redirect && submissionResult?.redirect_url) 
                              ? submissionResult.redirect_url 
                              : null
          },
          submission_data: form.data(),
          completion_time: completionTime
        })
        
        // Send message to parent if in iframe
        if (isIframe) {
          window.parent.postMessage(payload, '*')
        }
        // Also send to current window for potential internal listeners
        window.postMessage(payload, '*')
      }
      
      // 10. Handle redirect if server response includes redirect info
      if (import.meta.client && submissionResult?.redirect && submissionResult?.redirect_url) {
        window.location.href = submissionResult.redirect_url
      }
      
      return submissionResult // Return result from submission composable

    } catch (error) {
      // Restart partial submission sync if there was an error and it's enabled
      if (!import.meta.server && toValue(config).enable_partial_submissions) {
        partialSubmissionService.startSync()
      }
      
      // Handle validation or submission errors using validation composable's handler
      validation.onValidationFailure({
        fieldGroups: structure.fieldGroups.value,
        setPageIndexCallback: (index) => { state.currentPage = index },
        timerService: timer
      })
      state.isProcessing = false 
      throw error
    }
  }

  /** Resets the form to its initial state for refilling. */
  const restart = async () => {
    state.isSubmitted = false
    state.currentPage = 0
    state.isProcessing = false
    form.reset() // Reset vForm data
    form.errors.clear() // Clear vForm errors
    timer.reset() // Reset timer via composable
    timer.start() // Restart timer
    
    // Restart partial submission if enabled
    if (!import.meta.server && toValue(config).enable_partial_submissions) {
      partialSubmissionService.stopSync() // This will sync immediately before stopping
      partialSubmissionService.startSync() // Start fresh sync
    }
  }
  
  // Clean up when component using the manager is unmounted
  if (import.meta.client) {
    onBeforeUnmount(() => {
      if (toValue(config).enable_partial_submissions) {
        partialSubmissionService.stopSync()
      }
    })
  }

  // --- Exposed API --- 
  return {
    // Reactive State & Config
    state,          // Core state (currentPage, isProcessing, isSubmitted)
    config,         // Form configuration (ref)
    form,           // The vForm instance (from useForm)
    strategy,       // Current mode strategy (computed)
    pendingSubmission: pendingSubmissionService, // Expose pendingSubmission service
    partialSubmission: partialSubmissionService, // Expose partialSubmission service with debounced sync

    // UI-related properties
    darkMode,       // Dark mode setting
    setDarkMode: (isDark) => { darkMode.value = isDark }, // Method to update dark mode

    // Composables (Expose if direct access needed, often not necessary)
    structure,
    payment,        // Expose payment service

    // Core Methods
    initialize,
    nextPage,
    previousPage,
    submit,
    restart,

    // Convenience Computed Getters for vForm state
    data: computed(() => form.data()),
    errors: computed(() => form.errors),
    isDirty: computed(() => form.isDirty),
    busy: computed(() => form.busy), // Or potentially combine with state.isProcessing
  }
} 