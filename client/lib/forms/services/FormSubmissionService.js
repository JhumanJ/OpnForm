/**
 * @fileoverview Service responsible for managing the final form submission process.
 * It prepares data, interacts with backend APIs via vForm, handles post-submission
 * side effects (like analytics, redirects, clearing state), and respects the FormModeStrategy.
 */
// services/form/services/FormSubmissionService.js
// Placeholder - Full implementation needed based on requirements

import { useAmplitude } from '~/composables/useAmplitude'
import { useConfetti } from '~/composables/useConfetti'
import { useIsIframe } from '~/composables/useIsIframe'
import { pendingSubmission } from "~/composables/forms/pendingSubmission.js"
import { usePartialSubmission } from "~/composables/forms/usePartialSubmission.js" // Assuming vForm instance is passed correctly
import clonedeep from "clone-deep"
import { ref } from 'vue'

export class FormSubmissionService {
  constructor(form, formData) {
    this.form = form
    this.formData = formData // vForm instance
    // Instantiate composables needed for submission logic
    this.amplitude = useAmplitude()
    this.confetti = useConfetti()
   
    // Initialize pending/partial submission handlers - ensure they are client-side safe
    this.pendingSubmission = pendingSubmission(this.form)
    this.partialSubmission = usePartialSubmission(this.form, this.formData)
  }

  /**
   * Handles the complete form submission process.
   * @param {Object} options - Contains submission context like { formModeStrategy, completionTime, captchaToken (optional) }
   * @returns {Promise<Object>} - Resolves with { success: true, submissionData: {}, backendResponse: {} } or rejects with { success: false, error: {} }
   */
  async submit(options = {}) {
    const { formModeStrategy, completionTime, captchaToken } = options

    // 1. Check Strategy: Should we perform actual submission?
    if (!formModeStrategy.validation?.performActualSubmission) {
      console.log('Submission skipped: performActualSubmission is false for the current mode.')
      // Return a simulated success for modes like PREVIEW or TEST
      return { success: true, submissionData: this.formData.data(), backendResponse: { simulated: true } }
    }

    console.log('Starting form submission process...')

    // 2. Prepare Data
    const submissionData = { ...this.formData.data() }

    // Inject completion time
    if (completionTime !== undefined) {
      this.formData.completion_time = completionTime // Add directly to vForm instance
    }

    // Inject submission_id for editable submissions
    if (this.form.editable_submissions && this.form.submission_id) {
      this.formData.submission_id = this.form.submission_id
    }

    // Inject submission_hash for partial submissions
    if (this.form.enable_partial_submissions) {
      const hash = this.partialSubmission.getSubmissionHash()
      if (hash) {
        this.formData.submission_hash = hash
      }
      // Stop syncing partial data before final submission
      this.partialSubmission.stopSync()
    }

    // Inject Captcha token if provided
    if (captchaToken) {
      // Assuming backend expects a specific field name, e.g., 'captcha_token'
      this.formData['captcha_token'] = captchaToken // Add directly to vForm instance
    }

    // 3. Execute Submission using vForm
    const submitUrl = `/forms/${this.form.slug}/answer`
    try {
      console.log(`Submitting form data to ${submitUrl}`)
      // vForm's post method handles the request and initial success/error handling
      const backendResponse = await this.formData.post(submitUrl)
      console.log('Submission successful. Backend response:', backendResponse)

      // 4. Post-Submission Actions (Success)
      this.handleSuccessfulSubmission(backendResponse)

      // Return structured success data
      return { success: true, submissionData: this.formData.data(), backendResponse }

    } catch (error) {
      console.error('Submission failed:', error)
      // 5. Post-Submission Actions (Failure)
      this.handleFailedSubmission(error)

      // Rethrow or return structured error
      // vForm already populates errors, just signal failure
      throw { success: false, error: error?.data || error }
    }
  }

  /**
   * Handles actions after a successful backend submission.
   * @param {Object} backendResponse - The data returned from the successful POST request.
   */
  handleSuccessfulSubmission(backendResponse) {
    console.log('Handling successful submission...')
    // Log event
    this.amplitude.logEvent('form_submission', {
      workspace_id: this.form.workspace_id,
      form_id: this.form.id
    })

    // Post message for iframe communication
    const payload = clonedeep({
      type: 'form-submitted',
      form: {
        slug: this.form.slug,
        id: this.form.id,
        // Extract redirect URL safely from backend response
        redirect_target_url: (this.form.is_pro && backendResponse?.redirect && backendResponse?.redirect_url) ? backendResponse.redirect_url : null
      },
      submission_data: this.formData.data(), // Get final data from vForm
      completion_time: this.formData.completion_time // Get completion time from vForm
    })

    if (useIsIframe() && import.meta.client) {
      window.parent.postMessage(payload, '*')
      console.log('Posted message to parent window.')
    }
    if (import.meta.client) {
        window.postMessage(payload, '*') // Also post to current window if needed
    }

    // Clear pending/auto-saved submission state
    this.pendingSubmission.remove()
    this.pendingSubmission.removeTimer()
    console.log('Cleared pending submission data and timer.')

    // Handle redirects (client-side only)
    if (backendResponse?.redirect && backendResponse?.redirect_url && import.meta.client) {
      console.log(`Redirecting to: ${backendResponse.redirect_url}`)
      window.location.href = backendResponse.redirect_url
      // Note: Execution might stop here due to redirect
    }

    // Trigger confetti
    if (this.form.confetti_on_submission) {
      this.confetti.play()
      console.log('Played confetti.')
    }

    // Potentially handle first submission modal trigger logic if needed here,
    // though it might be better handled by the component based on the returned `is_first_submission` flag.
  }

  /**
   * Handles actions after a failed backend submission.
   * @param {Object} error - The error object from the failed request.
   */
  handleFailedSubmission(error) {
    console.log('Handling failed submission...')
    // Restart partial submission syncing if enabled
    if (this.form.enable_partial_submissions) {
      this.partialSubmission.startSync()
      console.log('Restarted partial submission sync.')
    }

    // Note: vForm automatically handles setting errors on `this.formData.errors`.
    // Additional alert logic can be triggered by the component catching the error.
  }
} 