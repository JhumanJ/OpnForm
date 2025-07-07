<template>
  <span
    v-if="value"
    class="-mb-2"
  >
    <UButton
      :to="paymentUrl"
      size="xs"
      variant="link"
      icon="i-heroicons-credit-card-20-solid"
      trailing-icon="i-heroicons-arrow-top-right-on-square-20-solid"
      label="Payment"
      target="_blank"
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
