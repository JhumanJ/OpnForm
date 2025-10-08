<template>
  <div class="w-full flex items-stretch grow min-h-0 z-10">
    <div v-if="isLeft" class="hidden md:block w-1/2 relative overflow-hidden">
      <BlockMediaLayout :image="image" :fallback-height="null" />
    </div>
    <div class="w-full md:w-1/2 flex items-center px-6">
      <div class="w-full max-w-2xl mx-auto">
        <slot />
      </div>
    </div>
    <div v-if="!isLeft" class="hidden md:block w-1/2 relative overflow-hidden">
      <BlockMediaLayout :image="image" :fallback-height="null" />
    </div>
  </div>
</template>

<script setup>
import BlockMediaLayout from '../BlockMediaLayout.vue'

const props = defineProps({
  image: { type: Object, required: true },
  side: { type: String, default: 'left' } // 'left' | 'right'
})

const isLeft = computed(() => (props.side || 'left') === 'left')
</script>


