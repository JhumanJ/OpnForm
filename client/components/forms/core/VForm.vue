<template>
  <form>
    <slot />
  </form>
</template>

<script setup>
/**
 * Used to pass props to children input components
 */
import ThemeBuilder from "~/lib/forms/themes/ThemeBuilder.js"

const props = defineProps({
  themeName: { type: String, default: 'default' },
  size: { type: String, default: "md" },
  borderRadius: { type: String, default: "small" }
})

// Provide legacy theme object for backwards compatibility
const theme = computed(() => (new ThemeBuilder(props.themeName, {size: props.size, borderRadius: props.borderRadius})).getAllComponents())
provide('theme', theme)

// Also provide individual theme props for new tailwind-variants approach
provide('formThemeName', computed(() => props.themeName))
provide('formSize', computed(() => props.size))  
provide('formBorderRadius', computed(() => props.borderRadius))
</script>
