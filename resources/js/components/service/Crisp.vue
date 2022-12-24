<template />

<script>
export default {
  name: 'Crisp',

  computed: {
    isIframe () {
      return window.location !== window.parent.location || window.frameElement
    }
  },

  watch: {},

  mounted () {
    this.loadCrisp()
  },

  methods: {
    loadCrisp () {
      if (this.isIframe || !window.config.crisp_website_id) return

      window.CRISP_WEBSITE_ID = window.config.crisp_website_id

      const script = document.createElement('script')
      script.setAttribute('src', 'https://client.crisp.chat/l.js')
      script.setAttribute('id', 'crisp-widget')
      script.setAttribute('async', 1)
      document.head.appendChild(script)
    }
  }
}
</script>
