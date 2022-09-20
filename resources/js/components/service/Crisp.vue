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
      if (this.isIframe) return

      window.$crisp = []
      window.CRISP_WEBSITE_ID = '94219d77-06ff-4aec-b07a-5bf26ec8fde1'

      const script = document.createElement('script')
      script.setAttribute('src', 'https://client.crisp.chat/l.js')
      script.setAttribute('id', 'crisp-widget')
      script.setAttribute('async', 1)
      document.head.appendChild(script)
    }
  }
}
</script>
