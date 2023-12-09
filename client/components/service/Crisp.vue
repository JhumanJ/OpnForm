<template />

<script>
export default {
  name: 'Crisp',

  setup () {
    return {
      isIframe: useIsIframe()
    }
  },

  mounted () {
    this.loadCrisp()
  },

  methods: {
    loadCrisp () {
      if (process.server) return
      if (this.isIframe || !this.$config.crisp_website_id) return

      window.CRISP_WEBSITE_ID = this.$config.crisp_website_id

      const script = document.createElement('script')
      script.setAttribute('src', 'https://client.crisp.chat/l.js')
      script.setAttribute('id', 'crisp-widget')
      script.setAttribute('async', 1)
      document.head.appendChild(script)
    }
  }
}
</script>
