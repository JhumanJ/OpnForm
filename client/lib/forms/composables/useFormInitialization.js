import { watch, toValue } from 'vue';

/**
 * @fileoverview Composable for initializing form data, with complete handling of 
 * form state persistence, URL parameters, and default values.
 */
export function useFormInitialization(formConfig, form, pendingSubmission) {
  /**
   * Applies URL parameters to the form data.
   * @param {URLSearchParams} params - The URL search parameters.
   */
  const applyUrlParameters = (params) => {
    if (!params) return;
    
    console.log('Applying URL parameters');
    
    // First, handle regular parameters
    params.forEach((value, key) => {
      // Skip array parameters for now
      if (key.endsWith('[]')) return;
      
      try {
        // Try to parse JSON if the value starts with '{'
        const parsedValue = (typeof value === 'string' && value.startsWith('{')) 
          ? JSON.parse(value) 
          : value;
          
        form[key] = parsedValue;
        console.log(`URL parameter set: ${key}=${value}`);
      } catch (e) {
        // If parsing fails, use the original value
        form[key] = value;
      }
    });
    
    // Handle array parameters (key[])
    const paramKeys = [...new Set([...params.keys()])];
    paramKeys.forEach(key => {
      if (key.endsWith('[]')) {
        const arrayValues = params.getAll(key);
        if (arrayValues.length > 0) {
          const baseKey = key.slice(0, -2);
          form[baseKey] = arrayValues;
          console.log(`Array parameter set: ${baseKey}=[${arrayValues.join(', ')}]`);
        }
      }
    });
  };

  /**
   * Applies default data to form fields that don't already have values.
   * @param {Object} defaultData - Default data object.
   */
  const applyDefaultValues = (defaultData) => {
    if (!defaultData || Object.keys(defaultData).length === 0) return;
    
    console.log('Applying default data');
    for (const key in defaultData) {
      if (Object.hasOwnProperty.call(defaultData, key) && form[key] === undefined) {
        form[key] = defaultData[key];
      }
    }
  };

  /**
   * Updates special fields like dates with today's date if configured.
   * @param {Array} fields - Form fields
   * @param {Object} formData - Current form data
   */
  const updateSpecialFields = (fields, formData) => {
    if (!fields) return;
    
    fields.forEach(field => {
      // Handle date fields with prefill_today
      if (field.type === 'date' && field.prefill_today) {
        formData[field.id] = new Date().toISOString();
      }
      // Handle matrix fields with prefill data
      else if (field.type === 'matrix' && !formData[field.id] && field.prefill) {
        formData[field.id] = {...field.prefill};
      }
    });
  };

  /**
   * Attempts to load form data from an existing submission.
   * @param {String} submissionId - ID of the submission to load
   * @returns {Promise<Boolean>} - Whether loading was successful
   */
  const tryLoadFromSubmissionId = async (submissionId) => {
    if (!submissionId) return false;
    
    console.log(`Loading data for submission ID: ${submissionId}`);
    try {
      await form.get(`/submissions/${submissionId}`);
      console.log('Submission data loaded successfully.');
      return true;
    } catch (error) {
      console.error(`Failed to load submission: ${error}`);
      form.reset(); // Reset if load failed
      return false;
    }
  };

  /**
   * Attempts to load form data from pendingSubmission in localStorage.
   * @returns {Boolean} - Whether loading was successful
   */
  const tryLoadFromPendingSubmission = () => {
    // Skip on server or if pendingSubmission is not available
    if (import.meta.server || !pendingSubmission) {
      console.log('Skipping pendingSubmission check on server or missing pendingSubmission object');
      return false;
    }
    
    // Check if auto-save is enabled for this form
    if (!pendingSubmission.enabled?.value) {
      console.log('PendingSubmission is disabled (auto_save is false)');
      return false;
    }
    
    // Get the saved data
    const pendingData = pendingSubmission.get();
    console.log('pendingSubmission found:', pendingData ? 'Yes' : 'No', 
                pendingData ? `(keys: ${Object.keys(pendingData).join(', ')})` : '');
    
    if (!pendingData || Object.keys(pendingData).length === 0) {
      return false;
    }
    
    console.log('Loading data from pending submission in localStorage');
    form.resetAndFill(pendingData);
    return true;
  };

  /**
   * Main method to initialize the form data.
   * Follows a clear priority order:
   * 1. Load from submission ID (if provided)
   * 2. Load from pendingSubmission (localStorage) - client-side only
   * 3. Apply URL parameters
   * 4. Apply default values for fields
   * 
   * @param {Object} options - Initialization options
   * @param {String} [options.submissionId] - ID of submission to load
   * @param {URLSearchParams} [options.urlParams] - URL parameters
   * @param {Object} [options.defaultData] - Default data to apply
   * @param {Array} [options.fields] - Form fields for special handling
   */
  const initialize = async (options = {}) => {
    console.log('Initializing form data with options:', options);
    const config = toValue(formConfig);
    
    // 1. Reset form state
    form.reset();
    form.errors.clear();
    console.log('Form reset complete');
    
    // 2. Try loading from submission ID
    if (options.submissionId) {
      const loaded = await tryLoadFromSubmissionId(options.submissionId);
      if (loaded) return; // Exit if loaded successfully
    }
    
    // 3. Try loading from pendingSubmission
    if (tryLoadFromPendingSubmission()) {
      if (options.fields) {
        updateSpecialFields(options.fields, form.data());
      }
      return; // Exit if loaded successfully
    }
    
    // 4. Start with empty form data
    const formData = {};
    
    // 5. Apply URL parameters
    if (options.urlParams) {
      applyUrlParameters(options.urlParams);
    }
    
    // 6. Apply special field handling
    if (options.fields) {
      updateSpecialFields(options.fields, formData);
    }
    
    // 7. Apply default data from config or options
    const defaultData = options.defaultData || config?.default_data;
    if (defaultData) {
      for (const key in defaultData) {
        if (!formData[key]) { // Only if not already set
          formData[key] = defaultData[key];
        }
      }
    }
    
    // 8. Fill the form with the collected data
    if (Object.keys(formData).length > 0) {
      form.resetAndFill(formData);
    }
    
    console.log('Form initialization complete. Current form data:', form.data());
  };

  // Setup watcher for form data changes to save to localStorage - client-side only
  if (!import.meta.server && pendingSubmission && pendingSubmission.enabled?.value) {
    watch(() => form.data(), (newValue) => {
      console.log('Form data changed, saving to localStorage');
      pendingSubmission.set(newValue);
    }, { deep: true });
  }

  return {
    initialize,
    applyUrlParameters,
    applyDefaultValues
  };
} 