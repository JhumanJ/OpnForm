<template>
  <div class="w-full flex items-center px-6 sm:px-12 grow min-h-0 z-10">
    <div class="w-full max-w-6xl mx-auto flex items-stretch md:items-center justify-between gap-6 md:gap-8 flex-col md:flex-row">
      <!-- Content -->
      <div
        class="w-full max-w-2xl order-1"
        :class="isLeft ? 'md:order-2' : 'md:order-1'"
      >
        <slot />
      </div>

      <!-- Media: below content on mobile; left/right on desktop -->
      <div
        class="w-full  md:max-w-1/2 order-2"
        :class="isLeft ? 'md:order-1' : 'md:order-2'"
      >
        <div class="md:p-4 xl:p-6 mb-4" :class="ui.mediaContainer()">
          <BlockMediaLayout
            :image="image"
            :fallback-height="null"
            :class="ui.mediaComponent()"
            :img-class="ui.mediaImg()"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BlockMediaLayout from '../BlockMediaLayout.vue'
import { tv } from 'tailwind-variants'
import { sideMediaSmallTheme } from '~/lib/forms/themes/side-media-small.theme.js'

const props = defineProps({
  image: { type: Object, required: true },
  side: { type: String, default: 'left' }, // 'left' | 'right'
  borderRadius: { type: String, default: 'small' } // 'none' | 'small' | 'full'
})

const isLeft = computed(() => (props.side || 'left') === 'left')

const ui = computed(() => {
  return tv(sideMediaSmallTheme)({
    borderRadius: props.borderRadius || 'small'
  })
})
</script>


