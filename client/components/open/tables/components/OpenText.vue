<template>
  <span>
    {{ value }}
  </span>
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
