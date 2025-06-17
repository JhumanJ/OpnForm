<template>
  <div class="break-words whitespace-break-spaces" v-html="value" />
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
