<template>
  <span
    v-if="value"
    class="-mb-2"
  >
    <UButton
      :to="paymentUrl"
      icon="i-heroicons-arrow-top-right-on-square"
      size="xs"
      variant="link"
      label="View Payment"
      target="_blank"
      trailing
    />
  </span>
</template>

<script>
export default {
  components: { },
  props: {
    value: {
      type: Object,
    },
  },

  data() {
    return {}
  },

  computed: {
    paymentUrl() {
      if (!this.value) return null
      const isLocal = useRuntimeConfig().public.env === 'local'
      return `https://dashboard.stripe.com${isLocal ? '/test' : ''}/payments/${this.value}`
    },
  },
}
</script>
