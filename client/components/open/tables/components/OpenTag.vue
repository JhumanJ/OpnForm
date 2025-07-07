<template>
  <UBadge
    :id="opt"
    class="mb-1"
    size="sm"
    color="neutral"
    variant="soft"
  >
    {{ opt }}
  </UBadge>
</template>

<script>
export default {
  components: {},
  props: {
    opt: {
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


