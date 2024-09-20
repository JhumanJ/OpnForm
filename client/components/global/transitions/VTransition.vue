<template>
  <transition
    v-if="name === 'slideInUp'"
    enter-active-class="linear duration-300 overflow-hidden"
    enter-from-class="max-h-0"
    enter-to-class="max-h-screen"
    leave-active-class="linear duration-300 overflow-hidden"
    leave-from-class="max-h-screen"
    leave-to-class="max-h-0"
  >
    <slot />
  </transition>
  <transition
    v-else-if="name === 'fade'"
    mode="out-in"
    enter-active-class="transition-opacity duration-300"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition-opacity duration-300"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <slot />
  </transition>
</template>

<script setup>
const props = defineProps({
  name: {
    type: String,
    default: "slideInUp"
  }
})
</script>
