<template>
  <div class="flex items-center">
    <input
      :id="id || name"
      v-model="internalValue"
      :name="name"
      type="checkbox"
      class="rounded border-gray-500 w-10 h-10 cursor-pointer checkbox"
      :class="theme.CheckboxInput.size"
      :style="{ '--accent-color': color }"
      :disabled="disabled ? true : null"
    >
    <label
      :for="id || name"
      class="text-gray-700 dark:text-gray-300 ml-2"
      :class="{ '!cursor-not-allowed': disabled }"
    >
      <slot />
    </label>
  </div>
</template>

<script setup>
import { defineEmits, defineOptions, defineProps, onMounted, ref, watch, } from "vue"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"

defineOptions({
  name: "VCheckbox",
})

const props = defineProps({
  id: { type: String, default: null },
  name: { type: String, default: "checkbox" },
  modelValue: { type: [Boolean, String], default: false },
  disabled: { type: Boolean, default: false },
  theme: {
    type: Object, default: () => {
      const theme = inject("theme", null)
      if (theme) {
        return theme.value
      }
      return CachedDefaultTheme.getInstance()
    }
  },
  color: { type: String, default: null },
})

const emit = defineEmits(["update:modelValue", "click"])

const internalValue = ref(props.modelValue)

watch(
  () => props.modelValue,
  (val) => {
    internalValue.value = val
  },
)

watch(
  () => props.checked,
  (val) => {
    internalValue.value = val
  },
)

watch(
  () => internalValue.value,
  (val, oldVal) => {
    if (val === 0 || val === "0") val = false
    if (val === 1 || val === "1") val = true

    if (val !== oldVal) emit("update:modelValue", val)
  },
)

onMounted(() => {
  if (internalValue.value === null) internalValue.value = false
})
</script>
<style>
  .checkbox {
    accent-color: var(--accent-color);
  }
</style>
