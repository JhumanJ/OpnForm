import { computed, toValue } from 'vue';

/**
 * @fileoverview Composable for handling form validation logic.
 */
export function useFormValidation(formConfig, form, managerState, isLastPage) {

  const formRef = computed(() => toValue(form)); // Ensure reactivity with the form instance
  const configRef = computed(() => toValue(formConfig)); // Ensure reactivity with config

  /**
   * Reactive computed property to determine if CAPTCHA is required for the current state.
   * Depends on form config, manager state, and whether it's the last page.
   */
  const isCaptchaRequired = computed(() => {
    const config = configRef.value;
    if (!config?.use_captcha || !toValue(managerState)) {
      return false;
    }

    // Use the isLastPage computed property passed from useFormStructure
    if (!isLastPage.value) {
        return false;
    }

    // Assuming useRuntimeConfig is globally available or keys are passed differently
    // TODO: Refactor runtime config access if possible (e.g., pass keys via formConfig)
    let runtimeConfig = {};
    if (typeof useRuntimeConfig === 'function') {
        runtimeConfig = useRuntimeConfig().public;
    } else {
        console.warn('useRuntimeConfig not available in useFormValidation. Captcha keys might be missing.');
        // Attempt to get from formConfig as a fallback?
        runtimeConfig.recaptchaSiteKey = config.recaptcha_site_key;
        runtimeConfig.hCaptchaSiteKey = config.hcaptcha_site_key;
    }

    const provider = config.captcha_provider;
    if (provider === 'recaptcha') {
      return !!runtimeConfig.recaptchaSiteKey;
    } else if (provider === 'hcaptcha') {
      return !!runtimeConfig.hCaptchaSiteKey;
    }
    return false;
  });

  /**
   * Validates specific fields using the vForm instance's validate method.
   * @param {Array<String>} fieldIds - Array of field IDs to validate.
   * @param {String} [httpMethod='POST'] - HTTP method for the validation request.
   * @returns {Promise<boolean>} Resolves true if validation passes, rejects with errors if fails.
   */
  const validateFields = async (fieldIds, httpMethod = 'POST') => {
    if (!fieldIds || fieldIds.length === 0) {
      console.log('No fields provided for validation.');
      return true; // Nothing to validate
    }

    console.log(`Validating fields: [${fieldIds.join(', ')}] using ${httpMethod}`);
    const config = configRef.value;
    const validationUrl = `/forms/${config.slug}/answer`; // Use reactive config

    try {
      // Use the reactive formRef
      await formRef.value.validate(httpMethod, validationUrl, {}, fieldIds);
      console.log('Validation successful for fields:', fieldIds);
      return true; // Validation passed
    } catch (error) {
      console.log('Validation failed for fields:', fieldIds, 'Errors:', error?.data?.errors || error);
      // vForm handles setting errors. Just rethrow.
      throw error;
    }
  };

  /**
   * Validates fields on the current page based on the form mode strategy.
   * Excludes payment fields.
   * @param {Array<Object>} currentPageFields - Array of field objects for the current page.
   * @param {Object} formModeStrategy - The strategy object for the current mode.
   * @returns {Promise<boolean>} Resolves true if validation passes or isn't required, rejects otherwise.
   */
  const validateCurrentPage = async (currentPageFields, formModeStrategy) => {
    if (!formModeStrategy?.validation?.validateOnNextPage) {
      console.log('Validation skipped: validateOnNextPage is false for the current mode.');
      return true;
    }

    const fieldIdsToValidate = currentPageFields
      .filter(field => field && field.id && field.type !== 'payment')
      .map(field => field.id);

    if (fieldIdsToValidate.length === 0) {
      console.log('No fields to validate on the current page.');
      return true;
    }

    try {
      await validateFields(fieldIdsToValidate);
      return true;
    } catch (errors) {
      console.log('Validation errors on current page.');
      handleValidationError(errors); // Log or perform other actions
      throw errors; // Rethrow to signal failure
    }
  };

  /**
   * Validates all form fields before final submission, *unless* currently on the last page.
   * @param {Array<Object>} allFields - Array of all field objects in the form.
   * @param {Object} formModeStrategy - The strategy object for the current mode.
   * @param {boolean} isCurrentlyLastPage - Boolean indicating if the *current* state is the last page.
   * @returns {Promise<boolean>} Resolves true if validation passes or isn't required/skipped, rejects otherwise.
   */
  const validateSubmissionIfNotLastPage = async (allFields, formModeStrategy, isCurrentlyLastPage) => {
    console.log(`[validateSubmissionIfNotLastPage Check] Received isCurrentlyLastPage=${isCurrentlyLastPage}`);

    // Skip validation if we are already determined to be on the last page
    if (isCurrentlyLastPage) {
      console.log('Validation skipped: On last page.');
      return true;
    }

    // Skip validation if strategy doesn't require it
    if (!formModeStrategy?.validation?.validateOnSubmit) {
      console.log('Validation skipped: validateOnSubmit is false for the current mode.');
      return true;
    }

    const fieldIdsToValidate = allFields
      .filter(field => field && field.id && field.type !== 'payment')
      .map(field => field.id);

    if (fieldIdsToValidate.length === 0) {
      console.log('No fields to validate before submission (not on last page).');
      return true;
    }

    try {
      await validateFields(fieldIdsToValidate);
      return true;
    } catch (errors) {
      console.log('Validation errors before submission (not on last page).');
      handleValidationError(errors);
      throw errors;
    }
  };

  /**
   * Handles validation errors, primarily for logging.
   * Assumes vForm instance already populates its errors object.
   */
  const handleValidationError = (error) => {
    console.error("Validation Error encountered:", error?.data?.errors || error?.data || error?.message || error);
    // Scrolling logic should be handled in the UI component
  };

  /**
   * Finds the index of the first page containing validation errors.
   * @param {Array<Array<Object>>} fieldGroups - Nested array of fields per page (from useFormStructure).
   * @returns {number} Index of the first page with errors, or -1 if no errors found.
   */
  const findFirstPageWithError = (fieldGroups) => {
    const errors = formRef.value.errors;
    if (!errors || !errors.any()) {
      return -1; // No errors exist
    }
    if (!fieldGroups) return -1; // No groups to check

    for (let i = 0; i < fieldGroups.length; i++) {
      const pageHasError = fieldGroups[i]?.some(field => field && field.id && errors.has(field.id));
      if (pageHasError) {
        console.log(`First page with error found: index ${i}`);
        return i;
      }
    }
    console.log('No page found containing current validation errors.');
    return -1;
  };

  /**
   * Actions to perform on validation failure (e.g., during submit or page change).
   * @param {Object} context - Object containing { fieldGroups, timerService, setPageIndexCallback }.
   */
  const onValidationFailure = (context) => {
    console.warn('Executing onValidationFailure actions.');
    const { fieldGroups, timerService, setPageIndexCallback } = context;

    // Restart timer using the timer composable instance
    if (timerService && typeof timerService.start === 'function') {
      timerService.start();
      console.log('Form timer instructed to start via timer composable.');
    } else {
      console.log('timerService (composable) not available or invalid in context for restart.');
    }

    // Find and navigate to the first page with an error
    if (fieldGroups && fieldGroups.length > 1 && typeof setPageIndexCallback === 'function') {
      const errorPageIndex = findFirstPageWithError(fieldGroups);
      if (errorPageIndex !== -1 && errorPageIndex !== toValue(managerState)?.currentPage) {
        console.log(`Validation failed, navigating to error page index: ${errorPageIndex}`);
        setPageIndexCallback(errorPageIndex);
      } else if (errorPageIndex !== -1) {
          console.log(`Validation failed, error is on the current page (${errorPageIndex}). No navigation needed.`);
      }
    }

    // Scrolling is handled by the component listening for errors
  };

  // --- Exposed API ---
  return {
    // Computed Properties
    isCaptchaRequired,

    // Methods
    validateFields,
    validateCurrentPage,
    validateSubmissionIfNotLastPage,
    handleValidationError,
    findFirstPageWithError,
    onValidationFailure,
  };
} 