<template>
  <div class="border border-gray-300 rounded-xl flex p-1 relative">
    <button class="font-semibold block flex-grow cursor-pointer">
      <div
        class="p-2 px-3 rounded-lg transition-colors"
        :class="{'bg-blue-500 text-white': !modelValue}"
        @click="set(false)"
      >
        Monthly
      </div>
    </button>
    <button class="font-semibold block flex-grow cursor-pointer" @click="set(true)">
      <div
        class="p-2 px-4 rounded-lg transition-colors"
        :class="{'bg-blue-500 text-white': modelValue}"
      >
        Yearly
      </div>
    </button>
    <div class="absolute hidden sm:block text-gray-500 text-xs mt-12">
      Save 20% with annual plans
    </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps } from 'vue'

defineProps({
  modelValue: { type: Boolean, required: true }
})
const emit = defineEmits()

const set = (value) => {
  emit('update:modelValue', value)
}
</script>
