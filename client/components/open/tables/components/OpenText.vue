<template>
  <div v-html="value" />
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      type: String,
      required: true,
    },
  },
}
</script>
