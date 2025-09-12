<template>
  <div :style="colorStyle">
    <Icon
      v-show="isChecked"
      name="i-material-symbols-check-box"
      :class="checkedIconClasses"
    />
    <Icon
      v-show="!isChecked"
      name="i-material-symbols-check-box-outline-blank"
      :class="uncheckedIconClasses"
    />
  </div>
</template>

<script setup>
import { tv } from "tailwind-variants"
import { checkboxIconTheme } from "~/lib/forms/themes/checkbox-icon.theme.js"

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

// Create checkbox icon variants with UI prop merging
const checkboxIconVariants = computed(() => tv(checkboxIconTheme, props.ui))

// Single variant computation
const variantSlots = computed(() => {
  return checkboxIconVariants.value({
    size: resolvedSize.value
  })
})

// Use variant slots
const checkedIconClasses = computed(() => variantSlots.value.checkedIcon())
const uncheckedIconClasses = computed(() => variantSlots.value.uncheckedIcon())
</script>