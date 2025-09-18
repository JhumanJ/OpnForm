<template>
  <img
    v-if="image && image.url"
    :src="image.url"
    :alt="image.alt || ''"
    class="w-full h-full object-cover rounded-md"
    :style="imageStyle"
    draggable="false"
    @dragstart.prevent
  >
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
  image: { type: Object, required: false }
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
</script>



