<template>
  <div class="w-full flex items-center px-6 grow min-h-0 z-10 h-full">
    <div class="w-full max-w-6xl mx-auto flex items-center justify-between gap-8">
      <div v-if="isLeft" class="hidden md:block shrink-0">
        <div class="relative w-[420px] h-[236px] xl:w-[520px] xl:h-[292px] overflow-hidden rounded-md">
          <BlockMediaLayout :image="image" :fallback-height="null" />
        </div>
      </div>

      <div class="w-full max-w-2xl">
        <slot />
      </div>

      <div v-if="!isLeft" class="hidden md:block shrink-0">
        <div class="relative w-[420px] h-[236px] xl:w-[520px] xl:h-[292px] overflow-hidden rounded-md">
          <BlockMediaLayout :image="image" :fallback-height="null" />
        </div>
      </div>
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


