<template>
  <div
    role="button"
    @click.stop="onClick"
  >
    <div
      class="inline-flex items-center h-6 w-12 p-1 bg-gray-300 border rounded-full cursor-pointer focus:outline-none transition-all transform ease-in-out duration-100"
      :class="{ 'bg-nt-blue': props.modelValue }"
    >
      <div
        class="inline-block h-4 w-4 rounded-full bg-white shadow transition-all transform ease-in-out duration-150 rounded-2xl scale-100"
        :class="{ 'translate-x-5.5': props.modelValue }"
      />
    </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
})
const emit = defineEmits(['update:modelValue'])

function onClick() {
  if (props.disabled)
    return
  emit('update:modelValue', !props.modelValue)
}
</script>
