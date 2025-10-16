<template>
  <label
    :for="nativeFor"
    :class="ui.label()"
  >
    <slot>
      <span class="align-baseline">{{ label }}</span>
      <span
        v-if="required"
        :class="ui.requiredDot()"
        aria-hidden="true"
      >
        <Icon name="i-ix-mandatory" />
      </span>
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
  theme: { type: String, default: 'default' },
  size: { type: String, default: 'md' },
  ui: {type: Object, default: () => ({})}
})

// OPTIMIZED: Single computed following Nuxt UI pattern
const ui = computed(() => {
  return tv(inputLabelTheme, props.ui)({
    theme: props.theme,
    size: props.size,
    uppercaseLabels: props.uppercaseLabels
  })
})

</script>
