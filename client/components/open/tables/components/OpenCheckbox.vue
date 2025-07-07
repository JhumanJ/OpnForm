<template>
  <UBadge
    v-if="value === true"
    color="success"
    variant="soft"
    class="mx-auto"
    size="sm"
  >
    Yes
  </UBadge>
  <UBadge
    v-else-if="value === false"
    color="error"
    variant="soft"
    class="mx-auto"
    size="sm"
  >
    No
  </UBadge>
</template>

<script>
export default {
  components: {},
  props: {
    value: {
      type: Boolean,
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
