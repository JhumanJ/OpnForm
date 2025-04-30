import { toValue } from 'vue';

/**
 * @fileoverview Composable for initializing form data, including handling URL params and default values.
 */
export function useFormInitialization(formConfig, form) {

  /**
   * Sets URL parameters as hidden fields in the form data.
   * @param {URLSearchParams} params - The URL search parameters.
   */
  const _setUrlParams = (params) => {
    const config = toValue(formConfig);
    if (!config?.save_url_parameters || !params) return;

    params.forEach((value, key) => {
      // Only add parameters explicitly listed in formConfig.save_url_parameters
      if (config.save_url_parameters.includes(key)) {
        form.set(key, value); // Assuming form.set handles adding/updating
        console.log(`URL parameter set: ${key}=${value}`);
      }
    });
  };

  /**
   * Applies default data to the form, potentially overriding URL params if specified.
   * @param {Object} defaultData - Default data object.
   */
  const _applyDefaultData = (defaultData) => {
    if (!defaultData || Object.keys(defaultData).length === 0) return;

    console.log('Applying default data:', defaultData);
    for (const key in defaultData) {
      if (Object.hasOwnProperty.call(defaultData, key)) {
        // Here you might decide if defaultData always overrides or only if the key doesn't exist
        // Current behavior: Overrides existing values (including URL params)
        form.set(key, defaultData[key]);
      }
    }
  };

  /**
   * Initializes the form data.
   * - Resets the form state.
   * - Sets data from a previous submission if submissionId is provided.
   * - Applies URL parameters.
   * - Applies default data.
   * @param {Object} options - Initialization options.
   * @param {String} [options.submissionId] - ID of a previous submission to load.
   * @param {URLSearchParams} [options.urlParams] - URL parameters.
   * @param {Object} [options.defaultData] - Default data to apply.
   */
  const initialize = async (options = {}) => {
    console.log('Initializing form data with options:', options);
    const config = toValue(formConfig); // Get current config value

    // 1. Reset form state (using the passed form instance)
    form.reset();
    form.errors.clear();
    console.log('Form instance reset.');

    // 2. Load existing submission if submissionId is provided
    if (options.submissionId) {
      console.log(`Loading data for submission ID: ${options.submissionId}`);
      try {
        // Assuming form.get makes an API call to fetch submission data
        await form.get(`/submissions/${options.submissionId}`);
        console.log('Submission data loaded successfully.');
      } catch (error) {
        console.error(`Failed to load submission data for ID ${options.submissionId}:`, error);
        // Decide how to handle failure: clear form, show error, etc.
        form.reset(); // Reset again if load failed
      }
    }

    // 3. Apply URL Parameters (if not loading a submission or if designed to override)
    // Check if submission was loaded. If so, URL params might not be needed or should be applied carefully.
    if (!options.submissionId && options.urlParams) {
       _setUrlParams(options.urlParams);
    }

    // 4. Apply Default Data
    // Apply default data *after* potential submission loading and URL params.
    // This allows default data to act as overrides or fill in missing fields.
    _applyDefaultData(options.defaultData || config?.default_data);

    console.log('Form initialization complete. Current form data:', form.data());
  };

  // Expose the core initialization function
  return {
    initialize,
  };
} 