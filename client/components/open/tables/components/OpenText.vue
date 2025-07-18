<template>
  <div 
    class="text-sm text-neutral-900" 
    v-html="value" 
  />
</template>

<script setup>
defineProps({
  value: {
    type: [String, Number],
    required: false,
    default: null
  },
})
</script>
