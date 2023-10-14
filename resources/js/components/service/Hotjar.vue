<template />

<script>
import { mapGetters } from 'vuex'

export default {

  name: 'Hotjar',

  watch: {
    authenticated () {
      if (this.authenticated) {
        this.loadHotjar()
      }
    }
  },

  mounted () {
    this.loadHotjar()
  },

  methods: {
    loadHotjar () {
      if (!this.authenticated || this.isIframe) return

      (function (h, o, t, j, a, r) {
        h.hj = h.hj || function () {
          (h.hj.q = h.hj.q || []).push(arguments)
        }
        h._hjSettings = { hjid: 2449591, hjsv: 6 }
        a = o.getElementsByTagName('head')[0]
        r = o.createElement('script')
        r.async = 1
        r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv
        a.appendChild(r)
      })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=')
    }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check'
    }),
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    }
  }
}
</script>
