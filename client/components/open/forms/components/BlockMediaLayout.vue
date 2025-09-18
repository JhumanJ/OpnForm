<template>
  <div class="relative w-full h-full" :style="wrapperStyle">
    <VTransition name="fade">
      <div
        v-if="image && image.url && !isLoaded"
        class="absolute inset-0 bg-neutral-500"
      />
    </VTransition>
    <img
      v-if="image && image.url"
      :src="image.url"
      :alt="image.alt || ''"
      :class="[imgClass, { 'opacity-0': !isLoaded }]"
      :style="imageStyle"
      draggable="false"
      @dragstart.prevent
      @load="onLoad"
      @error="onError"
    >
  </div>
  
</template>

<script setup>
/*
  BlockMediaLayout
  Props:
    - image: {
        url: string,
        alt?: string,
        layout?: 'between'|'right-small'|'left-small'|'right-split'|'left-split'|'background',
        focal_point?: { x: number, y: number }, // 0..100
        brightness?: number // -100..100
      }
*/

const props = defineProps({
  image: { type: Object, required: false },
  // min-height to reserve space before image loads; accepts number (px) or CSS size string
  fallbackHeight: { type: [String, Number], default: '12rem' },
  // classes applied to the <img> element
  imgClass: { type: String, default: 'w-full h-full object-cover transition-opacity duration-300' }
})

const isLoaded = ref(false)

const onLoad = () => {
  isLoaded.value = true
}

const onError = () => {
  isLoaded.value = false
}

watch(() => props.image?.url, () => {
  isLoaded.value = false
})

const imageStyle = computed(() => {
  const x = props.image?.focal_point?.x ?? 50
  const y = props.image?.focal_point?.y ?? 50
  const b = props.image?.brightness ?? 0 // -100..100
  const brightnessScale = Math.max(0, Math.min(2, (b + 100) / 100))
  return {
    objectPosition: `${x}% ${y}%`,
    filter: `brightness(${brightnessScale})`
  }
})

const wrapperStyle = computed(() => {
  const h = props.fallbackHeight
  if (h === null || h === undefined || h === '') return {}
  const value = typeof h === 'number' ? `${h}px` : h
  return { minHeight: value }
})
</script>



