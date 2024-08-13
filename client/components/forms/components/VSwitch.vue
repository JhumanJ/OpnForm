<template>
  <div
    :id="id || name"
    :aria-labelledby="id || name"
    role="checkbox"
    :aria-checked="props.modelValue"
    class="flex"
    @click.stop="onClick"
  >
    <div
      class="inline-flex items-center bg-gray-300 rounded-full cursor-pointer focus:outline-none transition-all transform ease-in-out duration-100"
      :class="[{ 'toggle-switch': props.modelValue }, theme.SwitchInput.containerSize]"
      :style="{ '--accent-color': props.color }"
    >
      <div
        class="inline-block h-4 w-4 rounded-full bg-white transition-all transform ease-in-out duration-150 scale-100"
        :class="{ [theme.SwitchInput.translatedClass]: props.modelValue}"
      />
    </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps } from "vue"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"

const props = defineProps({
  id: { type: String, default: null },
  name: { type: String, default: "checkbox" },
  modelValue: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  color: { type: String, default: '#3B82F6' },
  theme: {
    type: Object, default: () => {
      const theme = inject("theme", null)
      if (theme) {
        return theme.value
      }
      return CachedDefaultTheme.getInstance()
    }
  },
})
const emit = defineEmits(["update:modelValue"])

function onClick() {
  if (props.disabled) return
  emit("update:modelValue", !props.modelValue)
}
</script>
<style>
  .toggle-switch {
    background-color: var(--accent-color);
  }
</style>
