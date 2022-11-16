/**
 * Base mixin for all Vue components
 */
import debounce from 'debounce'

export default {
  computed: {},

  metaInfo () {
    const info = {
      meta: this.metaTags ?? []
    }
    if (this.metaTitle) {
      info.title = this.metaTitle
      info.meta = [
        ...info.meta,
        { vmid: 'og:title', property: 'og:title', content: this.metaTitle },
        { vmid: 'twitter:title', property: 'twitter:title', content: this.metaTitle }
      ]
    }
    if (this.metaDescription) {
      info.meta = [
        ...info.meta,
        { vmid: 'description', name: 'description', content: this.metaDescription },
        { vmid: 'og:description', property: 'og:description', content: this.metaDescription },
        { vmid: 'twitter:description', property: 'twitter:description', content: this.metaDescription }
      ]
    }

    return info
  },

  methods: {
    /**
     * Creates a debounced function that delays invoking a callback.
     */
    debouncer: debounce((callback) => callback(), 500),

    /**
     * Show an error message.
     */
    alertError (message, autoClose=10000) {
      this.$notify(
        {
          title: 'Error',
          text: message,
          type: 'error',
        }, autoClose)
    },

    /**
     * Show a success message.
     */
    alertSuccess (message, autoClose=10000) {
      this.$notify(
        {
          title: 'Success',
          text: message,
          type: 'success',
        }, autoClose)
    },

    /**
     * Show a warning message.
     */
    alertWarning (message, autoClose=10000) {
      this.$notify(
        {
          title: 'Warning',
          text: message,
          type: 'warning',
        }, autoClose)
    },

    /**
     * Show confirmation message.
     */
    alertConfirm (message, success, failure) {
      this.$root.alert.type = 'confirmation'
      this.$root.alert.autoClose = false
      this.$root.alert.message = message
      this.$root.alert.confirmationProceed = success
      this.$root.alert.confirmationCancel = failure

      window.scrollTo({ top: 0, behavior: 'smooth' })
    },

    /**
     * Show confirmation message.
     */
    closeAlert () {
      this.$root.alert = {
        type: null,
        autoClose: 0,
        message: '',
        confirmationProceed: null,
        confirmationCancel: null
      }
    }
  }
}
