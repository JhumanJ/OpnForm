<template>
  <div 
    class="flex items-center justify-between group relative cursor-pointer hover:bg-neutral-100 rounded-md px-2 py-1.5 w-full"
    :style="{ width: `var(--header-${column.id}-size, auto)` }"
  >
    <UTooltip 
      :text="column.columnDef.header"
      class="flex-1 min-w-0"
    >
      <div class="flex items-center gap-1">
        <BlockTypeIcon 
          v-if="column.columnDef.type"
          :type="column.columnDef.type"
          bg-class="bg-transparent"
          text-class="text-neutral-500"
          class="flex-shrink-0"
        />
        <span 
          class="truncate block text-sm text-neutral-500 font-medium"
          :class="{ 'line-clamp-none': isWrapped }"
        >
          {{ column.columnDef.header }}
        </span>
      </div>
    </UTooltip>
  
    <div
      v-if="column.getCanResize()"
      @mousedown="handleResizeStart"
      @touchstart="handleResizeStart"
      class="w-1 h-full absolute top-0 right-0 bg-primary-500/50 rounded cursor-col-resize select-none touch-none opacity-0 group-hover:opacity-100"
      :class="{ 'opacity-100': column.getIsResizing() }"
    />
  </div>
</template>

<script setup>
import BlockTypeIcon from '~/components/open/forms/components/BlockTypeIcon.vue'

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  columnPreferences: {
    type: Object,
    default: null,
  },
  isWrapped: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['resize-start'])

const handleResizeStart = (event) => {
  emit('resize-start', props.column, event)
}
</script> 