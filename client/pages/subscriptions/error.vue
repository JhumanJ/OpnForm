<template />

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'

export default {
  components: { },
  layout: 'default',
  middleware: 'auth',

  setup () {
    useOpnSeoMeta({
      title: 'Error'
    })

    const authStore = useAuthStore()
    return {
      authenticated : computed(() => authStore.check),
    }
  },

  data: () => ({
  }),

  mounted () {
    this.$router.push({ name: 'pricing' })
    useAlert().error('Unfortunately we could not confirm your subscription. Please try again and contact us if the issue persists.')
  },

  computed: {}
}
</script>
