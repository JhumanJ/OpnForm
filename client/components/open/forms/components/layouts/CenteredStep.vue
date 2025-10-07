<template>
  <div class="w-full flex items-center px-6 grow min-h-0 z-10 h-full relative">
    <div v-if="background && background.url" class="absolute inset-0 -z-10 pointer-events-none">
      <BlockMediaLayout :image="background" :fallback-height="null" />
    </div>
    <div class="w-full max-w-2xl mx-auto">
      <slot />
    </div>
  </div>
</template>

<script setup>
import BlockMediaLayout from '../BlockMediaLayout.vue'

defineProps({
  background: { type: Object, required: false, default: null }
})
</script>


