<template>
  <div class="w-full flex items-center px-6 sm:px-12 grow min-h-0 z-10">
    <div class="w-full max-w-6xl mx-auto flex items-center justify-between gap-8">
      <div v-if="isLeft" class="hidden md:block shrink-0 basis-1/2 max-w-1/2">
        <div class="p-4 xl:p-6" :class="ui.mediaContainer()">
          <BlockMediaLayout
            :image="image"
            :fallback-height="null"
            :class="ui.mediaComponent()"
            :img-class="ui.mediaImg()"
          />
        </div>
      </div>

      <div class="w-full max-w-2xl">
        <slot />
      </div>

      <div v-if="!isLeft" class="hidden md:block shrink-0 basis-1/2 max-w-1/2">
        <div class="p-4 xl:p-6" :class="ui.mediaContainer()">
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


