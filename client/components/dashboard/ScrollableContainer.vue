<template>
  <div class="relative">
    <!-- Top Fade -->
    <VTransition name="fade">
      <div
        v-if="showTopFade"
        :class="[
          'absolute top-0 left-0 right-0 z-10 pointer-events-none',
          topFadeClasses
        ]"
      />
    </VTransition>

    <!-- Scrollable Content -->
    <div 
      ref="scrollContainer"
      :class="[
        'overflow-y-auto',
        maxHeightClass
      ]"
    >
      <slot />
    </div>

    <!-- Bottom Fade -->
    <VTransition name="fade">
      <div
        v-if="showBottomFade"
        :class="[
          'absolute bottom-0 left-0 right-0 z-10 pointer-events-none',
          bottomFadeClasses
        ]"
      />
    </VTransition>
  </div>
</template>

<script setup>
import VTransition from '@/components/global/transitions/VTransition.vue'
import { useScroll, useResizeObserver } from '@vueuse/core'

const props = defineProps({
  maxHeightClass: {
    type: String,
    default: 'max-h-80'
  },
  topFadeHeight: {
    type: String,
    default: 'h-8'
  },
  bottomFadeHeight: {
    type: String,
    default: 'h-8'
  },
  fadeClass: {
    type: String,
    default: 'from-white to-transparent'
  }
})

// Scroll fade detection
const scrollContainer = ref(null)
const { y: scrollY } = useScroll(scrollContainer)
const contentHeight = ref(0)
const containerHeight = ref(0)

const showTopFade = computed(() => scrollY.value > 0)
const showBottomFade = computed(() => {
  if (containerHeight.value === 0 || contentHeight.value === 0) return false
  return scrollY.value < contentHeight.value - containerHeight.value - 1 // -1 for subpixel precision
})

const topFadeClasses = computed(() => {
  return `${props.topFadeHeight} bg-gradient-to-b ${props.fadeClass}`
})

const bottomFadeClasses = computed(() => {
  return `${props.bottomFadeHeight} bg-gradient-to-t ${props.fadeClass}`
})

// Update content dimensions when container resizes
useResizeObserver(scrollContainer, (entries) => {
  const entry = entries[0]
  containerHeight.value = entry.contentRect.height
  if (scrollContainer.value) {
    contentHeight.value = scrollContainer.value.scrollHeight
  }
})

// Expose container ref for parent components that might need it
defineExpose({
  scrollContainer
})
</script> 