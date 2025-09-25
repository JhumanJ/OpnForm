<template>
  <UTooltip
    v-if="show"
    text="Drag to resize"
    :content="{ side: tooltipSide }"
  >
    <div
      ref="handleRef"
      :class="[
        'absolute top-0 h-full w-1 hover:bg-blue-300 cursor-col-resize transition-colors duration-150 group z-10',
        direction === 'left' 
          ? 'right-0' 
          : 'left-0'
      ]"
      @mousedown="$emit('start-resize', $event)"
    >
      <div class="absolute inset-y-0 -right-1 group-hover:bg-blue-500 group-hover:opacity-20" />
    </div>
  </UTooltip>
</template>

<script setup>
defineEmits(['start-resize'])

const props = defineProps({
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

const tooltipSide = computed(() => props.direction === 'left' ? 'right' : 'left')

const handleRef = ref(null)

defineExpose({
  handleRef
})
</script>