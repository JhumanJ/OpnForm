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

<script setup>
import OpenTag from "./OpenTag.vue"

const props = defineProps({
  value: {
    type: [String, Object, Array],
    required: false,
    default: null
  },
  property: {
    type: Object,
    required: true
  }
})

const valueIsObject = computed(() => {
  return Array.isArray(props.value) || (typeof props.value === "object" && props.value !== null)
})
</script>
