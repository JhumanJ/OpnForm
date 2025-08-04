<template>
  <div
    v-if="show"
    ref="handleRef"
    :class="[
      'hover:bg-blue-500 cursor-col-resize transition-colors duration-150 group',
      direction === 'left' 
        ? 'flex-shrink-0 relative' 
        : 'absolute left-0 top-0 w-1 h-full bg-neutral-200 -translate-x-1 z-10'
    ]"
    @mousedown="$emit('start-resize', $event)"
  >
    <div class="absolute inset-y-0 -left-1 -right-1 group-hover:bg-blue-500 group-hover:opacity-20" />
  </div>
</template>

<script setup>
defineEmits(['start-resize'])

defineProps({
  show: {
    type: Boolean,
    default: true
  },
  direction: {
    type: String,
    default: 'right',
    validator: (value) => ['left', 'right'].includes(value)
  }
})

const handleRef = ref(null)

defineExpose({
  handleRef
})
</script>