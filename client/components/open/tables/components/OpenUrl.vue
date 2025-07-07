<template>
  <a
    class="text-sm text-neutral-900 underline decoration-neutral-300"
    :href="value"
    target="_blank"
    rel="nofollow"
  >{{ value }}</a>
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

  data() {
    return {}
  },

  computed: {},
  mounted() {},

  methods: {},
}
</script>
