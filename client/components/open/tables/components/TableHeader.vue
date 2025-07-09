<template>
  
    <div 
      class="flex items-center justify-between group cursor-pointer hover:bg-neutral-100 rounded-md px-2 py-1.5 w-full"
      :style="{ maxWidth: `var(--header-${column.id}-size, auto)`, 'minWidth': '80px'}"
    >
    <UPopover arrow>
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
          <!-- Individual column popover -->
          <template #content>
            <ColumnHeaderPopover
              :column="column"
              :table-state="tableState"
            />
          </template>
        </UPopover>
    
      <div
        v-if="column.getCanResize()"
        @mousedown="handleResizeStart"
        @touchstart="handleResizeStart"
        class="w-1 h-[80%] absolute right-0 bg-primary-500/50 rounded cursor-col-resize select-none touch-none opacity-0 group-hover:opacity-100"
        :class="{ 'opacity-100': isResizing }"
      />
    </div>
</template>

<script setup>
import BlockTypeIcon from '~/components/open/forms/components/BlockTypeIcon.vue'
import ColumnHeaderPopover from './ColumnHeaderPopover.vue'

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  tableState: {
    type: Object,
    required: true,
  },
  isWrapped: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['resize'])

const isResizing = ref(false)

const handleResizeStart = (event) => {
  event.preventDefault()
  event.stopPropagation()
  
  isResizing.value = true
  const startX = event.clientX || event.touches[0]?.clientX
  const startWidth = props.column.getSize()
    
  const handleMouseMove = (moveEvent) => {
    const currentX = moveEvent.clientX || moveEvent.touches[0]?.clientX
    const diff = currentX - startX
    const newWidth = Math.max(60, startWidth + diff) // Minimum width of 60px
        
    // Emit the new size
    emit('resize', props.column.id, newWidth)
  }
  
  const handleMouseUp = () => {
    isResizing.value = false
    document.removeEventListener('mousemove', handleMouseMove)
    document.removeEventListener('mouseup', handleMouseUp)
    document.removeEventListener('touchmove', handleMouseMove)
    document.removeEventListener('touchend', handleMouseUp)
    
  }
  
  document.addEventListener('mousemove', handleMouseMove)
  document.addEventListener('mouseup', handleMouseUp)
  document.addEventListener('touchmove', handleMouseMove)
  document.addEventListener('touchend', handleMouseUp)
}
</script> 