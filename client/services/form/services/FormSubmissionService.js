export class FormSubmissionService {
  constructor(form, formData, options = {}) {
    this.form = form
    this.formData = formData
    this.options = {
      isIframe: false,
      pendingSubmission: null,
      confetti: null,
      onSuccess: () => {},
      onFailure: () => {},
      ...options
    }
  }

  /**
   * Handle the form submission
   */
  async submit() {
    if (this.formData.busy) return false

    try {
      this.formData.busy = true
      const data = await this.formData.post('/forms/' + this.form.slug + '/answer')
      
      await this.handleSuccess(data)
      return { success: true, data }
    } catch (error) {
      this.handleError(error)
      return { success: false, error }
    } finally {
      this.formData.busy = false
    }
  }

  /**
   * Handle successful submission
   */
  async handleSuccess(data) {
    // Log analytics event
    useAmplitude().logEvent('form_submission', {
      workspace_id: this.form.workspace_id,
      form_id: this.form.id
    })

    // Prepare submission payload
    const payload = {
      type: 'form-submitted',
      form: {
        slug: this.form.slug,
        id: this.form.id,
        redirect_target_url: (this.form.is_pro && data.redirect && data.redirect_url) ? data.redirect_url : null
      },
      submission_data: this.formData.data(),
      completion_time: this.formData['completion_time']
    }

    // Handle iframe messaging
    if (this.options.isIframe) {
      window.parent.postMessage(payload, '*')
    }
    window.postMessage(payload, '*')

    // Clean up pending submission
    if (this.options.pendingSubmission) {
      this.options.pendingSubmission.remove()
      this.options.pendingSubmission.removeTimer()
    }

    // Handle redirect if needed
    if (data.redirect && data.redirect_url) {
      window.location.href = data.redirect_url
    }

    // Show confetti if enabled
    if (this.form.confetti_on_submission && this.options.confetti) {
      this.options.confetti.play()
    }

    // Call success callback
    if (this.options.onSuccess) {
      this.options.onSuccess(data)
    }
  }

  /**
   * Handle submission error
   */
  handleError(error) {
    console.error('Form submission error:', error)

    // Handle validation errors
    if (error.response?.data?.errors) {
      Object.keys(error.response.data.errors).forEach(field => {
        this.formData.errors.set(field, error.response.data.errors[field][0])
      })
    }

    // Show error alert
    if (error.response?.data?.message) {
      useAlert().error(error.response.data.message)
    } else {
      useAlert().error('An error occurred while submitting the form. Please try again.')
    }

    // Call failure callback
    if (this.options.onFailure) {
      this.options.onFailure(error)
    }
  }

  /**
   * Reset the submission state
   */
  reset() {
    this.formData.errors.clear()
    this.formData.busy = false
  }
}
