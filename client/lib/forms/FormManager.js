/**
 * @fileoverview Main orchestrator for form operations.
 * Initializes and coordinates various form services (Structure, Init, Validation, Payment, Submission, Timer)
 * based on the provided form configuration and mode. Manages core reactive state like currentPage and isProcessing.
 */
import { reactive, computed, nextTick } from 'vue'
import { useForm } from '~/composables/useForm.js' // Adjust path if needed
import { FormMode, createFormModeStrategy } from './FormModeStrategy'
import { FormStructureService } from './services/FormStructureService'
import { FormInitializationService } from './services/FormInitializationService'
import { FormValidationService } from './services/FormValidationService'
import { FormSubmissionService } from './services/FormSubmissionService'
import { FormPaymentService } from './services/FormPaymentService'
import { FormTimerService } from './services/FormTimerService'

export class FormManager {
  /**
   * @param {Object} formConfig - The raw configuration object for the form.
   * @param {String} mode - The operating mode (e.g., FormMode.LIVE, FormMode.PREVIEW).
   * @param {Object} refs - Optional object containing refs needed by services (e.g., { formTimerRef, captchaRef }).
   */
  constructor(formConfig, mode = FormMode.LIVE) {
    console.log(`Initializing FormManager in ${mode} mode.`);
    this.config = reactive(formConfig)
    this.mode = mode

    // Core vForm instance for data and errors
    this.form = useForm() 

    // Determine operational strategy based on mode
    this.strategy = createFormModeStrategy(mode)

    // Reactive state managed by the manager
    this.state = reactive({
      currentPage: 0,
      isSubmitted: false,
      isProcessing: false, // Unified flag for any async operation (validation, payment, submission)
      // completionTime is managed by timerService
    })
    
    // Store registered component APIs reactively
    this.registeredComponents = reactive({}); 

    // Instantiate services
    // Pass the manager's reactive state and other services as needed
    this.structureService = new FormStructureService(this.config, this.state)
    this.initService = new FormInitializationService(this.config, this.form)
    // Pass structureService instance to validation service
    this.validationService = new FormValidationService(this.config, this.form, this.state, this.structureService.computedIsLastPage) 
    this.paymentService = new FormPaymentService(this.config, this.form)
    this.submissionService = new FormSubmissionService(this.config, this.form)
    this.timerService = new FormTimerService(this.config)
  }

  // Method for components to register their APIs
  registerComponent(id, api) {
      if (!id) {
          console.warn("FormManager: Attempted to register component without an ID.");
          return;
      }
      console.log(`FormManager: Registering component API for ID: ${id}`);
      this.registeredComponents[id] = api;
  }

  // --- Public API --- 

  /**
   * Initializes the form data and starts the timer.
   * @param {Object} options - Initialization options (e.g., { submissionId, urlParams, defaultData }).
   */
  async initialize(options = {}) {
    this.state.isProcessing = true;
    this.state.isSubmitted = false; // Reset submitted state on init
    this.state.currentPage = 0; // Reset page on init
    console.log('FormManager initializing...', options);
    
    try {
      // Initialize form data using the dedicated service
      await this.initService.initialize(options)
      console.log('Form data initialized.');
      
      // Reset and start the timer
      this.timerService.reset(); 
      this.timerService.start();
      console.log('Form timer started.');

    } catch (error) {
        console.error("Error during FormManager initialization:", error);
        // Handle initialization error appropriately, maybe set an error state
    } finally {
        this.state.isProcessing = false;
    }
  }

  /** Getter for reactive form data */
  get data() {
    return this.form.data()
  }

  /** Getter for reactive form errors */
  get errors() {
    return this.form.errors
  }

  /**
   * Navigates to the next page, performing validation and payment processing as needed.
   * @param {Function} setPageIndexCallback - Callback to update the component's page index state.
   */
  async nextPage() {
    if (this.state.isProcessing) return false;
    console.log(`Attempting to navigate to next page from ${this.state.currentPage}`);
    this.state.isProcessing = true;

    try {
      const currentPageFields = this.structureService.getPageFields(this.state.currentPage);
      const isLast = this.structureService.isLastPage(this.state.currentPage);

      // 1. Validate current page (if required by strategy)
      await this.validationService.validateCurrentPage(currentPageFields, this.strategy);
      console.log('Current page validation successful (or skipped).')

      // 2. Process payment (if applicable on this page)
      const paymentBlock = this.structureService.getPaymentBlock(this.state.currentPage);
      if (paymentBlock) {
          console.log('Payment block found, processing payment...');
          const paymentResult = await this.paymentService.processPayment(paymentBlock);
          if (!paymentResult.success) {
            console.error('Payment processing failed:', paymentResult.error);
            // Payment errors are often set directly on the form errors by the service
            // We might still need to trigger the standard validation failure handling
            throw paymentResult.error || new Error('Payment failed'); 
          }
          console.log('Payment processing successful (or skipped).');
      }

      // 3. Move to the next page if validation and payment were successful
      if (!isLast) {
        this.state.currentPage++;
        console.log(`Navigated to page ${this.state.currentPage}`);
        // Optionally scroll to top - maybe better handled by component reacting to page change?
        // if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' });
      }
      this.state.isProcessing = false;
      return true;

    } catch (error) {
      console.error('Error during nextPage navigation:', error);
      // Handle validation or payment errors using the validation service's failure handler
      this.validationService.onValidationFailure({
          fieldGroups: this.structureService.getFieldGroups(),
          setPageIndexCallback: (index) => { this.state.currentPage = index; }, // Pass callback to update manager state
          timerService: this.timerService // Pass the timer service instance
      });
      this.state.isProcessing = false;
      return false;
    }
  }

  /** Navigates to the previous page */
  previousPage() {
    if (this.state.currentPage > 0 && !this.state.isProcessing) {
      this.state.currentPage--;
      console.log(`Navigated to previous page ${this.state.currentPage}`);
       // Optionally scroll to top
       // if (import.meta.client) window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }

  /**
   * Attempts the final form submission.
   * @param {Object} submitOptions - Optional object containing additional data for submission (e.g., { captchaToken }).
   * @returns {Promise<Object>} - Result from FormSubmissionService.
   */
  async submit(submitOptions = {}) {
    if (this.state.isProcessing) return Promise.reject('Processing');
    console.log('Attempting final form submission...');
    this.state.isProcessing = true;

    try {
        // 1. Stop Timer & Get Time
        this.timerService.stop();
        const completionTime = this.timerService.getCompletionTime();
        console.log(`Form submitted. Completion time: ${completionTime}s`);

        // 2. Final Validation (if required by strategy)
        // Read the computed value from the authoritative source service
        const isOnLastPage = this.structureService.computedIsLastPage.value;
        console.log(`[FormManager.submit] Reading structureService.computedIsLastPage.value: ${isOnLastPage}`);
        
        await this.validationService.validateSubmissionIfNotLastPage(
            this.config.properties || [], 
            this.strategy,
            isOnLastPage // Pass the read boolean value
        );
        
        // 3. Get Captcha Token (Updated Logic)
        let captchaToken = submitOptions.captchaToken;
        // Use the computed property from the validation service
        const requiresCaptcha = this.validationService.computedIsCaptchaRequired.value;
        
        if (requiresCaptcha && !captchaToken) {
            // Access registered components map directly
            const captchaApi = this.registeredComponents['captcha'];
            if (captchaApi && typeof captchaApi.getToken === 'function') {
                console.log("Captcha required, calling registered component getToken...");
                try {
                    captchaToken = await captchaApi.getToken();
                    if (!captchaToken) {
                        throw new Error("Registered captcha component returned empty token.");
                    }
                    console.log("Captcha token obtained via registered component.");
                } catch (captchaError) {
                    console.error("Error obtaining captcha token from registered component:", captchaError);
                    throw new Error("Could not process Captcha. Please try again.");
                }
            } else {
                console.warn("Captcha required, but no captcha component API or getToken method is registered.");
                throw new Error("Captcha is required but could not be processed.");
            }
        }

        // 4. Perform Submission (Pass token)
        const submissionResult = await this.submissionService.submit({
          formModeStrategy: this.strategy,
          completionTime: completionTime,
          captchaToken: captchaToken,
          ...submitOptions
        });
        console.log('Submission service call successful.');

        // 5. Update State on Success
        this.state.isSubmitted = true;
        this.state.isProcessing = false;
        return submissionResult; // Return the result from the submission service

    } catch (error) {
        console.error('Error during final submission:', error);
        // Handle validation or submission errors using the validation service's failure handler
         this.validationService.onValidationFailure({
            fieldGroups: this.structureService.getFieldGroups(),
            setPageIndexCallback: (index) => { this.state.currentPage = index; },
            timerService: this.timerService // Pass the timer service instance
        });
        this.state.isProcessing = false;
        // Ensure the error being thrown is useful
        if (error instanceof Error) {
             throw error;
        } else if (error && error.message) {
             throw new Error(error.message);
        } else {
            throw new Error('An unknown submission error occurred.');
        }
    }
  }
  
  /**
   * Resets the form to its initial state for refilling.
   * TODO: Define exact reset behavior - should it re-fetch defaults or just clear state?
   */
  async restart() {
      console.log('FormManager restarting...');
      this.state.isSubmitted = false;
      this.state.currentPage = 0;
      this.state.isProcessing = false; // Ensure processing is false
      this.form.reset(); // Reset vForm data to original values (if any)
      // Consider if a full re-initialization is needed based on requirements
      // await this.initialize({ refs: this.componentRefs }); // Option 1: Full re-init
      
      // Option 2: Just reset timer and potentially clear errors
      this.timerService.reset();
      this.timerService.start();
      this.form.errors.clear();
      
      console.log('FormManager restart complete.');
  }
} 