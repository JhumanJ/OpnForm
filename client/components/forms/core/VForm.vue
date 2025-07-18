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
})

const theme = computed(() => (new ThemeBuilder(props.themeName, {size: props.size})).getAllComponents())
provide('theme', theme)
</script>
