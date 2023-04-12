/**
 * Base mixin for all Vue components
 */
import debounce from 'debounce'
import Vue from 'vue'
import VueConfetti from 'vue-confetti'
Vue.use(VueConfetti)

export default {

  computed: {
    $crisp () {
      return window.$crisp
    }
  },

  methods: {
    /**
     * Creates a debounced function that delays invoking a callback.
     */
    debouncer: debounce((callback) => callback(), 500),

    /**
     * Show an error message.
     */
    alertError (message, autoClose = 10000) {
      this.$notify(
        {
          title: 'Error',
          text: message,
          type: 'error'
        }, autoClose)
    },

    /**
     * Show a success message.
     */
    alertSuccess (message, autoClose = 10000) {
      this.$notify(
        {
          title: 'Success',
          text: message,
          type: 'success'
        }, autoClose)
    },

    /**
     * Show a warning message.
     */
    alertWarning (message, autoClose = 10000) {
      this.$notify(
        {
          title: 'Warning',
          text: message,
          type: 'warning'
        }, autoClose)
    },

    /**
     * Show confirmation message.
     */
    alertConfirm (message, success, failure = ()=>{}, autoClose = 10000) {
      this.$notify(
        {
          title: 'Confirm',
          text: message,
          type: 'confirm',
          success,
          failure
        }, autoClose)
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
    },

    /**
     * Display confetti
     */
    playConfetti () {
      this.$confetti.start({ defaultSize: 6 })
      setTimeout(() => {
        this.$confetti.stop()
      }, 3000)
    }
  }
}
