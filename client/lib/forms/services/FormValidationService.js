/**
 * @fileoverview Service responsible for handling form validation logic based on the current FormModeStrategy.
 * It orchestrates validation calls using the vForm instance (Precognition) and provides
 * helpers for handling validation failures, like scrolling to errors.
 */
// services/form/services/FormValidationService.js
// Placeholder - Full implementation needed based on requirements

import { createFormModeStrategy } from '~/lib/forms/FormModeStrategy'
import { computed } from 'vue'
// Assuming useAlert is available/imported if needed for direct use, though often handled by component
// Assuming FormStructureService might be injected or passed if needed for complex scenarios

export class FormValidationService {
  constructor(formConfig, formData, managerState, structureService) {
    this.formConfig = formConfig // Raw form config
    this.formData = formData   // vForm instance
    this.managerState = managerState // Manager's reactive state
    this.structureService = structureService // FormStructureService instance
    
    // Computed property for captcha requirement
    this.computedIsCaptchaRequired = computed(() => {
        if (!this.formConfig.use_captcha || !this.structureService || !this.managerState) {
            return false;
        }
        // Use manager state for current page and structure service for last page check
        const isLast = this.structureService.isLastPage(this.managerState.currentPage);
        if (!isLast) {
            return false;
        }
        
        // Assuming useRuntimeConfig is accessible or keys are passed differently
        // Ideally, site keys should be part of formConfig or passed explicitly
        const config = useRuntimeConfig() 
        const provider = this.formConfig.captcha_provider
        if (provider === 'recaptcha') {
            return !!config.public.recaptchaSiteKey
        } else if (provider === 'hcaptcha') {
            return !!config.public.hCaptchaSiteKey
        }
        return false
    });
  }

  /**
   * Determines if captcha is configured and required for the current state.
   * Uses the computed property.
   * @deprecated Use computedIsCaptchaRequired instead.
   */
  isCaptchaRequired() {
    console.warn("`isCaptchaRequired` method is deprecated. Use `computedIsCaptchaRequired`.");
    return this.computedIsCaptchaRequired.value;
  }

  /**
   * Validates fields using the vForm instance's validate method (Precognition).
   * Handles logic based on the FormModeStrategy.
   * @param {Array<String>} fieldIds - Array of field IDs to validate.
   * @param {String} httpMethod - Usually 'POST' or 'PUT' for validation requests.
   * @returns {Promise<boolean>} - Resolves true if validation passes, rejects with errors if fails.
   */
  async validateFields(fieldIds, httpMethod = 'POST') {
    if (!fieldIds || fieldIds.length === 0) {
      console.log('No fields provided for validation.')
      return true // Nothing to validate
    }

    console.log(`Validating fields: [${fieldIds.join(', ')}] using ${httpMethod}`)

    // The vForm `validate` method handles the Precognition request
    // `validate(method, url, config = {}, fieldsToValidate = {})`
    const validationUrl = `/forms/${this.formConfig.slug}/answer` // Use formConfig for slug

    try {
      // Pass the fieldIds correctly to the vForm validate method
      await this.formData.validate(httpMethod, validationUrl, {}, fieldIds)
      console.log('Validation successful for fields:', fieldIds)
      return true // Validation passed
    } catch (error) {
      console.log('Validation failed for fields:', fieldIds, 'Errors:', error)
      // vForm's catch block already calls handleErrors and populates this.formData.errors
      // We just need to reject the promise so the caller knows it failed.
      throw error // Rethrow the error object containing validation messages
    }
  }

  /**
   * Validates fields on the current page, typically before navigating forward.
   * Excludes payment fields.
   * @param {Array<Object>} currentPageFields - Array of field objects for the current page.
   * @param {Object} formModeStrategy - The strategy object for the current mode.
   * @returns {Promise<boolean>} - Resolves true if validation passes or isn't required, rejects otherwise.
   */
  async validateCurrentPage(currentPageFields, formModeStrategy) {
    if (!formModeStrategy.validation?.validateOnNextPage) {
      console.log('Validation skipped: validateOnNextPage is false for the current mode.')
      return true // Skip validation if strategy doesn't require it
    }

    const fieldIdsToValidate = currentPageFields
      .filter(field => field && field.id && field.type !== 'payment') // Ensure field and id exist, exclude payment
      .map(field => field.id)

    if (fieldIdsToValidate.length === 0) {
      console.log('No fields to validate on the current page.')
      return true // No fields require validation on this page
    }

    try {
      await this.validateFields(fieldIdsToValidate)
      return true // Validation successful
    } catch (errors) {
      console.log('Validation errors on current page.')
      // Optionally call handleValidationError for additional logging or UI side-effects
      this.handleValidationError(errors)
      throw errors // Rethrow to signal failure to the caller (e.g., FormManager.nextPage)
    }
  }

   /**
   * Validates all form fields before final submission, based on strategy.
   * @param {Array<Object>} allFields - Array of all field objects in the form.
   * @param {Object} formModeStrategy - The strategy object for the current mode.
   * @returns {Promise<boolean>} - Resolves true if validation passes or isn't required, rejects otherwise.
   */
  async validateBeforeSubmit(allFields, formModeStrategy) {
    if (!formModeStrategy.validation?.validateOnSubmit) {
        console.log('Validation skipped: validateOnSubmit is false for the current mode.')
        return true;
    }

    const fieldIdsToValidate = allFields
        .filter(field => field && field.id && field.type !== 'payment') // Ensure field and id exist, exclude payment
        .map(field => field.id);

    if (fieldIdsToValidate.length === 0) {
        console.log('No fields to validate before submission.');
        return true;
    }

    try {
        await this.validateFields(fieldIdsToValidate);
        return true; // Validation successful
    } catch (errors) {
        console.log('Validation errors before submission.');
        this.handleValidationError(errors);
        throw errors; // Rethrow to signal failure
    }
  }


  /**
   * Handles validation errors (primarily for logging or triggering UI effects).
   * vForm instance already populates `this.formData.errors`.
   */
  handleValidationError(error) {
    // Log the error for debugging. UI effects like alerts are better handled in the component.
    console.error("Validation Error encountered:", error?.data?.errors || error?.data || error)
    // Trigger scroll to error after a short delay to allow DOM updates
    if (import.meta.client) {
        this.scrollToFirstError();
    }
  }

  /**
   * Scrolls the viewport to the first element with a common error class.
   * Should be called client-side only.
   */
  scrollToFirstError() {
    if (import.meta.server) return;
    // Use nextTick or setTimeout to ensure DOM reflects errors
    setTimeout(() => {
        // Use a selector common to fields with errors (adjust if needed)
        const firstErrorElement = document.querySelector('.form-group .has-error, .form-group [error]');
        if (firstErrorElement) {
          const headerOffset = 60; // Offset for fixed headers, adjust as needed
          const elementPosition = firstErrorElement.getBoundingClientRect().top;
          const offsetPosition = elementPosition + window.scrollY - headerOffset;

          window.scrollTo({
              top: offsetPosition,
              behavior: 'smooth'
          });
          console.log('Scrolled to first error element.')
        } else {
            console.log('No error element found to scroll to.')
        }
    }, 100); // Delay helps ensure element is rendered and styled
  }

  /**
   * Finds the index of the first page containing validation errors.
   * @param {Array<Array<Object>>} fieldGroups - Nested array of fields per page.
   * @returns {number} - Index of the first page with errors, or -1 if no errors found.
   */
  findFirstPageWithError(fieldGroups) {
    if (!this.formData.errors || !this.formData.errors.any()) {
      return -1; // No errors exist
    }
    for (let i = 0; i < fieldGroups.length; i++) {
        const pageHasError = fieldGroups[i].some(field => field && field.id && this.formData.errors.has(field.id));
        if (pageHasError) {
            console.log(`First page with error found: index ${i}`)
            return i;
        }
    }
    console.log('No page found containing current validation errors.')
    return -1; // No page contained the current errors
  }

  /**
   * Actions to perform on validation failure during submission or page change.
   * @param {Object} context - Object containing { fieldGroups, formTimerRef, setPageIndexCallback }
   */
  onValidationFailure(context) {
    console.warn('Executing onValidationFailure actions.');
    const { fieldGroups, formTimerRef, setPageIndexCallback } = context;

    // Restart timer if provided
    if (formTimerRef?.value?.startTimer) {
      formTimerRef.value.startTimer();
      console.log('Form timer restarted.');
    } else {
        console.log('Form timer ref not available or invalid for restart.');
    }

    // Find and navigate to the first page with an error
    if (fieldGroups && fieldGroups.length > 1 && typeof setPageIndexCallback === 'function') {
        const errorPageIndex = this.findFirstPageWithError(fieldGroups);
        if (errorPageIndex !== -1) {
            setPageIndexCallback(errorPageIndex);
            console.log(`Navigated to error page index: ${errorPageIndex}`);
        }
    } else {
        console.log('Field groups or setPageIndexCallback not available for error navigation.');
    }

    // Scroll to the first error field on the page
    this.scrollToFirstError();
  }
} 