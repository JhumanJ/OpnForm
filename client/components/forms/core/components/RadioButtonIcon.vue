<template>
  <div :style="colorStyle">
    <Icon
      v-show="isChecked"
      name="ic:round-radio-button-checked"
      :class="checkedIconClasses"
    />
    <Icon
      v-show="!isChecked"
      name="ic:round-radio-button-unchecked"
      :class="uncheckedIconClasses"
    />
  </div>
</template>

<script setup>
import { tv } from "tailwind-variants"
import { radioButtonIconTheme } from "~/lib/forms/themes/radio-button-icon.theme.js"

const props = defineProps({
  isChecked: {
    type: Boolean,
    required: true
  },
  color: {
    type: String,
    default: '#3B82F6'
  },
  // Theme configuration as strings for tailwind-variants
  size: {type: String, default: null}, 
  ui: {type: Object, default: () => ({})}
})

// Inject theme values for centralized resolution
const injectedSize = inject('formSize', null)

// Resolve size with proper reactivity
const resolvedSize = computed(() => {
  return props.size || injectedSize?.value || 'md'
})

// Color style for CSS custom property
const colorStyle = computed(() => ({
  '--form-color': props.color
}))

// Create radio button icon variants with UI prop merging
const radioButtonIconVariants = computed(() => tv(radioButtonIconTheme, props.ui))

// Single variant computation
const variantSlots = computed(() => {
  return radioButtonIconVariants.value({
    size: resolvedSize.value
  })
})

// Use variant slots
const checkedIconClasses = computed(() => variantSlots.value.checkedIcon())
const uncheckedIconClasses = computed(() => variantSlots.value.uncheckedIcon())
</script>