<template>
  <span
    v-if="value"
    class="-mb-2 flex flex-wrap gap-1"
  >
    <template v-if="valueIsObject">
      <open-tag
        v-for="(val, index) in value"
        :key="index + val"
        :opt="val"
      />
    </template>
    <open-tag
      v-else
      :opt="value"
    />
  </span>
</template>

<script>
import OpenTag from "./OpenTag.vue"

export default {
  components: { OpenTag },
  props: {
    value: {
      type: [String, Object, Array],
      required: false,
      default: null
    },
    property: {
      type: Object,
      required: true
    }
  },

  data() {
    return {}
  },

  computed: {
    valueIsObject() {
      return Array.isArray(this.value) || (typeof this.value === "object" && this.value !== null)
    },
  },
}
</script>
