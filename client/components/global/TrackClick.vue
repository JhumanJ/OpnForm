<template>
  <span @click="track">
    <slot />
  </span>
</template>

<script setup>
const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  properties: {
    type: Object,
    default: () => ({}),
  },
})

const { logEvent } = useAmplitude()

function track() {
  if (props.name) {
    logEvent(props.name, props.properties)
  }
}
</script> 