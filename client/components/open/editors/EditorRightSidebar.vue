<template>
  <transition @leave="(el, done) => sidebarMotion?.leave(done)">
    <div
      v-if="show"
      ref="sidebar"
      class="absolute shadow-lg shadow-neutral-800/30 top-0 h-[calc(100vh-53px)] right-0 lg:shadow-none lg:relative bg-white border-l overflow-y-scroll flex-shrink-0 z-30"
      :class="[
        isResizable ? '' : 'w-full md:w-1/2 lg:w-2/5',
        widthClass
      ]"
      :style="isResizable ? { width: sidebarWidth + 'px', minWidth: '280px', maxWidth: '600px' } : {}"
    >
      <!-- Resize handle - only show on large screens when resizable -->
      <div
        v-if="isResizable && isLargeScreen"
        ref="resizeHandle"
        class="absolute left-0 top-0 w-1 h-full bg-neutral-200 hover:bg-blue-400 cursor-col-resize transition-colors duration-150 -translate-x-1 group z-10"
        @mousedown="startResize"
      >
        <div class="absolute inset-y-0 -left-1 -right-1 group-hover:bg-blue-400 group-hover:opacity-20"></div>
      </div>
      
      <slot />
    </div>
  </transition>
</template>

<script setup>
import { slideRight, useMotion } from '@vueuse/motion'
import { watch, computed } from 'vue'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  widthClass: {
    type: String,
    default: 'md:max-w-[20rem]',
  },
  resizable: {
    type: Boolean,
    default: false,
  },
})

// Sidebar resizing state
const sidebarWidth = ref(350) // Default width for right sidebar
const isResizing = ref(false)
const sidebar = ref(null)
const resizeHandle = ref(null)
const sidebarMotion = ref(null)

// Breakpoint detection
const breakpoints = useBreakpoints(breakpointsTailwind)
const isLargeScreen = computed(() => breakpoints.greater('lg').value)
const isResizable = computed(() => props.resizable && isLargeScreen.value)

// Watch for show prop changes (existing functionality)
watch(
  () => props.show,
  (newVal) => {
    if (newVal) {
      nextTick(() => {
        sidebarMotion.value = useMotion(sidebar.value, slideRight)
      })
    }
  },
)

// Sidebar resizing methods
const startResize = (e) => {
  if (!isResizable.value) return
  
  isResizing.value = true
  document.addEventListener('mousemove', doResize)
  document.addEventListener('mouseup', stopResize)
  document.body.style.cursor = 'col-resize'
  document.body.style.userSelect = 'none'
  e.preventDefault()
}

const doResize = (e) => {
  if (!isResizing.value) return
  
  const containerRect = sidebar.value.parentElement.getBoundingClientRect()
  const newWidth = containerRect.right - e.clientX
  
  // Apply min/max width constraints
  const minWidth = 280
  const maxWidth = Math.min(600, containerRect.width * 0.6) // Max 60% of container width
  
  sidebarWidth.value = Math.max(minWidth, Math.min(maxWidth, newWidth))
  
  // Save to localStorage
  localStorage.setItem('formEditorRightSidebarWidth', sidebarWidth.value.toString())
}

const stopResize = () => {
  isResizing.value = false  
  document.removeEventListener('mousemove', doResize)
  document.removeEventListener('mouseup', stopResize)
  document.body.style.cursor = ''
  document.body.style.userSelect = ''
}

// Restore saved width on mount
onMounted(() => {
  const savedWidth = localStorage.getItem('formEditorRightSidebarWidth')
  if (savedWidth) {
    sidebarWidth.value = parseInt(savedWidth, 10)
  }
})

// Cleanup on unmount
onBeforeUnmount(() => {
  if (isResizing.value) {
    stopResize()
  }
})
</script>
