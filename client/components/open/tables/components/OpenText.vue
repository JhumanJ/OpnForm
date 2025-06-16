<template>
  <div class="wrap-anywhere break-all" v-html="value" />
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      type: [String, Number],
      required: false,
      default: null
    },
  },
}
</script>
