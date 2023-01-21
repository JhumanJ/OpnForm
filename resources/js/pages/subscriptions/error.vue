<template />

<script>
import { mapGetters } from 'vuex'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: { },
  layout: 'default',
  middleware: 'auth',
  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Error',
  }),

  mounted () {
    this.$router.push({ name: 'pricing' })
    this.alertError('Unfortunately we could not confirm your subscription. Please try again and contact us if the issue persists.')
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check'
    })
  }
}
</script>
