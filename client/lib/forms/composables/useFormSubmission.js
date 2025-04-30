import { toValue } from 'vue';

/**
 * @fileoverview Composable for handling the final form submission process.
 */
export function useFormSubmission(formConfig, form) {

  /**
   * Prepares the payload for submission.
   * Includes form data, completion time, and captcha token.
   * Filters out internal/temporary fields.
   * @param {Object} options - Options containing completionTime, captchaToken, etc.
   * @returns {Object} The payload object for the submission request.
   */
  const _preparePayload = (options = {}) => {
    const formData = toValue(form).data(); // Get current data
    const payload = { ...formData };

    // Add meta fields
    if (options.completionTime !== undefined) {
      payload.completion_time = options.completionTime;
    }
    if (options.captchaToken) {
      payload.captcha_token = options.captchaToken;
    }

    // Remove any internal/temporary fields (e.g., starting with _)
    for (const key in payload) {
      if (key.startsWith('_')) {
        delete payload[key];
      }
    }
    
    // TODO: Potentially add other metadata like user agent, timezone, etc.
    // payload.user_agent = navigator.userAgent;
    // payload.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    console.log('Prepared submission payload:', payload);
    return payload;
  };

  /**
   * Determines the correct submission URL based on form mode and config.
   * @param {Object} formModeStrategy - The strategy object for the current mode.
   * @returns {String} The submission URL.
   */
  const _getSubmitUrl = (formModeStrategy) => {
    const config = toValue(formConfig);
    if (formModeStrategy?.submission?.apiUrl) {
      // Allow strategy to override URL (e.g., for preview mode)
      return formModeStrategy.submission.apiUrl.replace('{slug}', config.slug);
    }
    // Default submission URL
    return `/forms/${config.slug}/answer`;
  };

  /**
   * Determines the HTTP method for submission based on form mode.
   * @param {Object} formModeStrategy - The strategy object.
   * @returns {String} HTTP method ('POST', 'PUT', etc.).
   */
  const _getHttpMethod = (formModeStrategy) => {
    return formModeStrategy?.submission?.method || 'POST'; // Default to POST
  };

  /**
   * Performs the form submission.
   * @param {Object} options - Submission options.
   * @param {Object} options.formModeStrategy - The strategy object.
   * @param {Number} [options.completionTime] - Form completion time in seconds.
   * @param {String} [options.captchaToken] - Captcha verification token.
   * @returns {Promise<Object>} The response data from the submission endpoint.
   * @throws {Error} If submission fails.
   */
  const submit = async (options = {}) => {
    const { formModeStrategy, ...payloadOptions } = options;
    if (!formModeStrategy) {
        throw new Error('Form mode strategy is required for submission.');
    }

    const url = _getSubmitUrl(formModeStrategy);
    const method = _getHttpMethod(formModeStrategy);
    const payload = _preparePayload(payloadOptions);

    console.log(`Submitting form via ${method} to ${url}`);

    try {
      // Use the vForm instance's submit method
      // form.submit(method, url, { data: payload }) might be needed depending on useForm implementation
      // Assuming form.submit directly takes method, url, payload
      const response = await toValue(form).submit(method, url, payload); 

      console.log('Submission successful:', response);
      // Optionally reset form data after successful submission based on strategy?
      if (formModeStrategy.submission?.resetAfterSubmit) {
          toValue(form).reset();
      }
      return response; // Return the successful response data
    } catch (error) {
      console.error('Submission failed:', error?.response?.data || error);
      // Errors should be automatically handled by vForm (setting form.errors)
      // Rethrow the original error object so callers can inspect it
      throw error; 
    }
  };

  // Expose the main submission function
  return {
    submit,
  };
} 