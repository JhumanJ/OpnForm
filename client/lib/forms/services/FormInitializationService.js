/**
 * @fileoverview Service responsible for initializing the form data based on various contexts,
 * such as default values, URL parameters, pending auto-saved data, or existing editable submissions.
 */
import { pendingSubmission } from '~/composables/forms/pendingSubmission.js'
import { useAlert } from '~/composables/useAlert'

export class FormInitializationService {
  constructor(form, formData) {
    this.form = form
    this.formData = formData 
  }

  async initialize(options = {}) {
    // Priority 1: Default data form provided directly
    if (options.defaultData) {
      console.log('Initializing form with defaultData')
      return this.applyDefaultData(options.defaultData)
    }

    // Priority 2: Editable submission (requires submissionId)
    if (this.form.editable_submissions && options.submissionId) {
      console.log(`Initializing form with submission data: ${options.submissionId}`)
      const data = await this.fetchSubmissionData(options.submissionId)
      if (data) {
        return this.applySubmissionData(data)
      }
      // If fetching fails, fall through to next priorities
    }

    // Priority 3: Pending/auto-saved submission
    if (this.form.auto_save && import.meta.client) { // Auto-save is client-side only
      const pendingData = this.getPendingSubmission()
      if (pendingData) {
        console.log('Initializing form with pending submission data')
        return this.applyPendingData(pendingData)
      }
    }

    // Priority 4: URL parameters and field default values
    console.log('Initializing form with default values and URL params')
    return this.applyDefaultValues(options.urlParams)
  }

  async fetchSubmissionData(submissionId) {
    try {
      // Assuming opnFetch is a global helper or needs to be imported
      const response = await opnFetch(`/forms/${this.form.slug}/submissions/${submissionId}`)
      // Ensure the response structure matches expectations
      if (response && response.data) {
         // Combine submission ID with fetched data
         return { submission_id: submissionId, id: submissionId, ...response.data };
      } else {
        console.error('Invalid response structure from fetchSubmissionData', response)
        useAlert().error('Could not load submission data.')
        return null
      }
    } catch (error) {
      console.error('Error fetching submission data:', error)
      if (error?.data?.errors) {
        useAlert().formValidationError(error.data)
      } else {
        useAlert().error(error?.data?.message || 'Failed to load submission data.')
      }
      return null
    }
  }

  getPendingSubmission() {
    if (!this.form || !import.meta.client) return null // Ensure running on client

    try {
        const pendingSub = pendingSubmission(this.form)
        const data = pendingSub.get()

        if (data && typeof data === 'object' && Object.keys(data).length !== 0) {
          return data
        }
    } catch (e) {
        console.error("Error getting pending submission:", e);
    }

    return null
  }

  applyDefaultData(defaultData) {
    // Assuming formData has a method like resetAndFill or similar
    this.formData.reset()
    this.formData.fill(defaultData)
    return true
  }

  applySubmissionData(data) {
    this.formData.reset()
    this.formData.fill(data)
    return true
  }

  applyPendingData(pendingData) {
    const initialData = { ...pendingData };
    // Update fields like date with prefill_today if they are not already in pendingData
    (this.form.properties || []).forEach(field => {
      if (field.type === 'date' && field.prefill_today && !(field.id in initialData)) {
        initialData[field.id] = new Date().toISOString().split('T')[0] // Format as YYYY-MM-DD
      }
    })

    this.formData.reset()
    this.formData.fill(initialData)
    return true
  }

  applyDefaultValues(urlParams = null) {
    const initialData = {}
    const clientUrlParams = import.meta.client ? (urlParams || new URLSearchParams(window.location.search)) : null;

    (this.form.properties || []).forEach(field => {
      // Skip internal fields unless explicitly needed
      if (field.type.startsWith('nf-') &&
          !['nf-page-body-input', 'nf-page-logo', 'nf-page-cover'].includes(field.type)) {
        return
      }

      let valueSet = false;
      
      // Handle URL Prefill first
      if (clientUrlParams) {
          valueSet = this.handleUrlPrefill(field, initialData, clientUrlParams);
      }
      
      // Handle Default Prefill only if not set by URL
      if (!valueSet) {
          this.handleDefaultPrefill(field, initialData);
      }
    })

    this.formData.reset()
    this.formData.fill(initialData)
    return true
  }

  handleUrlPrefill(field, formData, urlParams) {
    if (!urlParams || !field || !field.id) return false;

    const prefillValue = (() => {
      const val = urlParams.get(field.id)
      if (val === null || val === undefined) return null;
      try {
        // Attempt to parse if it looks like JSON
        return typeof val === 'string' && (val.startsWith('{') || val.startsWith('[')) ? JSON.parse(val) : val
      } catch (e) {
        return val // Return as string if JSON parsing fails
      }
    })()

    const arrayPrefillValue = urlParams.getAll(field.id + '[]')

    if (prefillValue !== null) {
        // Checkboxes expect boolean based on value
        if (field.type === 'checkbox') {
            formData[field.id] = this.parseBooleanValue(prefillValue);
        } else {
            formData[field.id] = prefillValue;
        }
        return true; // Value was set
    } else if (arrayPrefillValue.length > 0) {
        // Handle array values (e.g., for multi-select)
        formData[field.id] = arrayPrefillValue;
        return true; // Value was set
    }
    return false; // No value found in URL params
  }

  parseBooleanValue(value) {
    if (typeof value === 'boolean') return value;
    const lowerCaseValue = String(value).toLowerCase();
    return lowerCaseValue === 'true' || lowerCaseValue === '1';
  }

  handleDefaultPrefill(field, formData) {
    if (!field || field.id === undefined || field.id === null) return;

    // Skip if value already exists (e.g., from URL prefill)
    if (field.id in formData) return;

    if (field.type === 'date' && field.prefill_today) {
      formData[field.id] = new Date().toISOString().split('T')[0] // Format as YYYY-MM-DD
    } else if (field.type === 'matrix' && field.prefill) {
      // Ensure matrix prefill is cloned if it's an object/array
      formData[field.id] = typeof field.prefill === 'object' && field.prefill !== null
                           ? JSON.parse(JSON.stringify(field.prefill))
                           : field.prefill;
    } else if (field.prefill !== undefined && field.prefill !== null) {
      formData[field.id] = field.prefill
    }
    // Note: We don't explicitly set to null/undefined if no prefill exists,
    // the form object usually handles default initialization.
  }
} 