<template>
  <div
    v-if="show"
    ref="handleRef"
    :class="[
      'absolute top-0 h-full w-0.5 hover:bg-blue-300 cursor-col-resize transition-colors duration-150 group z-10',
      direction === 'left' 
        ? 'right-0' 
        : 'left-0'
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