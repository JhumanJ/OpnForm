<template />

<script>
import { mapGetters } from 'vuex'

export default {
  components: { },
  layout: 'default',
  middleware: 'auth',

  metaInfo () {
    return { title: 'Error' }
  },

  data: () => ({}),

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
