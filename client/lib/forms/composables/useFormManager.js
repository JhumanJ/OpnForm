import { reactive, computed, ref, toValue } from 'vue';
import { useForm } from '~/composables/useForm.js'; // Assuming useForm handles vForm setup
import { FormMode, createFormModeStrategy } from '../FormModeStrategy';
import { useFormStructure } from './useFormStructure';
import { useFormInitialization } from './useFormInitialization';
import { useFormValidation } from './useFormValidation';
import { useFormSubmission } from './useFormSubmission';
import { useFormPayment } from './useFormPayment';
import { useFormTimer } from './useFormTimer';

/**
 * @fileoverview Main orchestrator composable for form operations.
 * Initializes and coordinates various form composables (Structure, Init, Validation, etc.)
 * based on the provided form configuration and mode.
 */
export function useFormManager(initialFormConfig, mode = FormMode.LIVE) {
  console.log(`Initializing useFormManager in ${mode} mode.`);

  // --- Reactive State ---
  const config = ref(initialFormConfig); // Use ref for potentially replaceable config
  const form = useForm(); // Core vForm instance
  const strategy = computed(() => createFormModeStrategy(mode)); // Strategy based on mode

  const state = reactive({
    currentPage: 0,
    isSubmitted: false,
    isProcessing: false, // Unified flag for async ops
  });

  // Store registered component APIs (e.g., for Captcha, Stripe Elements)
  const registeredComponents = reactive({});

  // --- Instantiate Composables (Services) ---
  // Pass reactive refs/state where needed
  const timer = useFormTimer(config);
  const initialization = useFormInitialization(config, form);
  // Pass form.data() for structure logic dependency if needed, or the whole form instance
  const structure = useFormStructure(config, state, form); 
  const validation = useFormValidation(config, form, state, structure.isLastPage);
  const payment = useFormPayment(config, form);
  const submission = useFormSubmission(config, form);

  // --- Core Methods --- 

  /**
   * Registers a component's API (e.g., its ref) with the manager.
   * @param {String} id - Unique identifier for the component (e.g., 'captcha', 'stripeElement').
   * @param {Object} api - The component's exposed API (usually its ref).
   */
  const registerComponent = (id, api) => {
    if (!id) {
      console.warn("useFormManager: Attempted to register component without an ID.");
      return;
    }
    console.log(`useFormManager: Registering component API for ID: ${id}`);
    registeredComponents[id] = api;
  };

  /**
   * Initializes the form: loads data, resets state, starts timer.
   * @param {Object} options - Initialization options (passed to useFormInitialization).
   */
  const initialize = async (options = {}) => {
    state.isProcessing = true;
    state.isSubmitted = false;
    state.currentPage = 0;
    console.log('useFormManager initializing...', options);

    try {
      // Ensure latest config is used if it's replaceable
      // (Handled by passing config ref to initialization composable)
      await initialization.initialize(options);
      console.log('Form data initialized via composable.');

      timer.reset();
      timer.start();
      console.log('Form timer started via composable.');

    } catch (error) {
      console.error("Error during useFormManager initialization:", error);
      // Handle initialization error (e.g., set error state on form instance)
       form.errors.set('initialization', 'Failed to initialize form. Please refresh.');
    } finally {
      state.isProcessing = false;
    }
  };

  /**
   * Navigates to the next page, handling validation and payment intent creation.
   */
  const nextPage = async () => {
    if (state.isProcessing) return false;
    console.log(`Attempting to navigate to next page from ${state.currentPage}`);
    state.isProcessing = true;

    try {
      const currentPageFields = structure.getPageFields(state.currentPage);
      // Use computed isLastPage directly from structure composable
      const isCurrentlyLastPage = structure.isLastPage.value; 

      // 1. Validate current page
      await validation.validateCurrentPage(currentPageFields, strategy.value);
      console.log('Current page validation successful (or skipped).');

      // 2. Process payment (Create Payment Intent if applicable)
      const paymentBlock = structure.getPaymentBlock(state.currentPage);
      if (paymentBlock) {
        console.log('Payment block found, processing payment intent...');
        // Pass required refs if Stripe needs them now (unlikely for just intent creation)
        const paymentResult = await payment.processPayment(paymentBlock /*, potentially stripeRef, elementsRef */);
        if (!paymentResult.success) {
          console.error('Payment intent creation failed:', paymentResult.error);
          throw paymentResult.error || new Error('Payment intent creation failed');
        }
        console.log('Payment intent processed successfully.');
      }

      // 3. Move to the next page if not the last
      if (!isCurrentlyLastPage) {
        state.currentPage++;
        console.log(`Navigated to page ${state.currentPage}`);
        // Scrolling should be handled by the component watching currentPage
      } else {
           console.log('Already on the last page, cannot navigate next.');
      }
      state.isProcessing = false;
      return true;

    } catch (error) {
      console.error('Error during nextPage navigation:', error);
      // Use validation composable's failure handler
      validation.onValidationFailure({
        fieldGroups: structure.fieldGroups.value, // Pass reactive groups
        setPageIndexCallback: (index) => { state.currentPage = index; },
        timerService: timer // Pass the timer composable instance
      });
      state.isProcessing = false;
      return false;
    }
  };

  /** Navigates to the previous page */
  const previousPage = () => {
    if (state.currentPage > 0 && !state.isProcessing) {
      state.currentPage--;
      console.log(`Navigated to previous page ${state.currentPage}`);
      // Scrolling handled by component
    }
  };

  /**
   * Attempts the final form submission.
   * Handles timer stop, final validation, captcha, and submission call.
   * @param {Object} submitOptions - Optional extra data for submission.
   * @returns {Promise<Object>} Result from submission composable.
   */
  const submit = async (submitOptions = {}) => {
    if (state.isProcessing) {
        console.warn('Submission attempt ignored, already processing.');
        return Promise.reject('Processing');
    }
    console.log('Attempting final form submission via useFormManager...');
    state.isProcessing = true;

    try {
      // 1. Stop Timer & Get Time
      timer.stop();
      const completionTime = timer.getCompletionTime();
      console.log(`Form submit initiated. Completion time: ${completionTime}s`);

      // 2. Final Validation (if not on last page or required by strategy)
      const isCurrentlyLastPage = structure.isLastPage.value;
      console.log(`[useFormManager.submit] Checking validation. Is last page: ${isCurrentlyLastPage}`);
      await validation.validateSubmissionIfNotLastPage(
        config.value.properties || [],
        strategy.value,
        isCurrentlyLastPage // Pass the reactive value
      );
      console.log('Final validation successful (or skipped).');

      // 3. Get Captcha Token if required
      let captchaToken = submitOptions.captchaToken;
      const requiresCaptcha = validation.isCaptchaRequired.value;

      if (requiresCaptcha && !captchaToken) {
        const captchaApi = registeredComponents['captcha']; // Access registered component
        if (captchaApi && typeof captchaApi.getToken === 'function') {
          console.log("Captcha required, calling registered component getToken...");
          try {
            captchaToken = await captchaApi.getToken();
            if (!captchaToken) {
              throw new Error("Captcha component returned empty token.");
            }
            console.log("Captcha token obtained via registered component.");
          } catch (captchaError) {
            console.error("Error obtaining captcha token:", captchaError);
            throw new Error("Could not process Captcha. Please try again.");
          }
        } else {
          console.warn("Captcha required, but no compatible component registered at ID 'captcha'.");
          throw new Error("Captcha is required but could not be processed.");
        }
      }

      // 4. Perform Submission (using submission composable)
      const submissionResult = await submission.submit({
        formModeStrategy: strategy.value,
        completionTime: completionTime,
        captchaToken: captchaToken,
        ...submitOptions
      });
      console.log('Submission composable call successful.');

      // 5. Update State on Success
      state.isSubmitted = true;
      state.isProcessing = false;
      return submissionResult; // Return result from submission composable

    } catch (error) {
      console.error('Error during final submission process:', error);
      // Handle validation or submission errors using validation composable's handler
      validation.onValidationFailure({
        fieldGroups: structure.fieldGroups.value,
        setPageIndexCallback: (index) => { state.currentPage = index; },
        timerService: timer
      });
      state.isProcessing = false;
       // Rethrow a meaningful error
       const message = error?.response?.data?.message || error?.message || 'An unknown submission error occurred.';
       if (!form.errors.any()) { // Add a general error if vform didn't catch specific ones
           form.errors.set('submission', message);
       }
       throw new Error(message);
    }
  };

  /** Resets the form to its initial state for refilling. */
  const restart = async () => {
    console.log('useFormManager restarting...');
    state.isSubmitted = false;
    state.currentPage = 0;
    state.isProcessing = false;
    form.reset(); // Reset vForm data
    form.errors.clear(); // Clear vForm errors
    timer.reset(); // Reset timer via composable
    timer.start(); // Restart timer
    console.log('useFormManager restart complete.');
    // Note: Does not re-run initialize automatically. Call initialize() again if needed.
  };

  // --- Exposed API --- 
  return {
    // Reactive State & Config
    state,          // Core state (currentPage, isProcessing, isSubmitted)
    config,         // Form configuration (ref)
    form,           // The vForm instance (from useForm)
    strategy,       // Current mode strategy (computed)
    registeredComponents, // Registered child component APIs

    // Composables (Expose if direct access needed, often not necessary)
    structure,
    // validation,
    // timer,
    // payment,
    // submission,

    // Core Methods
    initialize,
    nextPage,
    previousPage,
    submit,
    restart,
    registerComponent,

    // Convenience Computed Getters for vForm state
    data: computed(() => form.data()),
    errors: computed(() => form.errors),
    isDirty: computed(() => form.isDirty),
    busy: computed(() => form.busy), // Or potentially combine with state.isProcessing
  };
} 