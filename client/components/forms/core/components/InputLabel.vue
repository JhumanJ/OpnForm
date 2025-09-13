<template>
  <label
    :for="nativeFor"
    :class="variantSlots.label()"
  >
    <slot>
      {{ label }}
      <span
        v-if="required"
        :class="variantSlots.requiredDot()"
      >*</span>
    </slot>
  </label>
</template>

<script setup>
import { tv } from "tailwind-variants"
import { inputLabelTheme } from "~/lib/forms/themes/input-label.theme.js"

defineOptions({
  name: "InputLabel"
})

const props = defineProps({
  nativeFor: { type: String, default: null },
  uppercaseLabels: { type: Boolean, default: false },
  required: { type: Boolean, default: false },
  label: { type: String, required: true },
  ui: {type: Object, default: () => ({})}
})

// Create input label variants with UI prop merging
const inputLabelVariants = computed(() => tv(inputLabelTheme, props.ui))

// Single variant computation
const variantSlots = computed(() => {
  return inputLabelVariants.value({
    uppercaseLabels: props.uppercaseLabels
  })
})

</script>
