<template />

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: { },
  layout: 'default',
  middleware: 'auth',
  mixins: [SeoMeta],

  setup () {
    const authStore = useAuthStore()
    return {
      authenticated : computed(() => authStore.check),
    }
  },

  data: () => ({
    metaTitle: 'Error',
  }),

  mounted () {
    this.$router.push({ name: 'pricing' })
    useAlert().error('Unfortunately we could not confirm your subscription. Please try again and contact us if the issue persists.')
  },

  computed: {}
}
</script>
