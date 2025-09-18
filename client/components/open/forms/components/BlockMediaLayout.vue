<template>
  <div v-if="image && image.url" class="block-media-layout">
    <component :is="layoutComponent">
      <template #image>
        <img
          :src="image.url"
          :alt="image.alt || ''"
          class="w-full h-full object-cover rounded-md"
          :style="imageStyle"
        />
      </template>
      <template #default>
        <slot />
      </template>
    </component>
  </div>
  <div v-else>
    <slot />
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
  image: { type: Object, required: false }
})

const layout = computed(() => props.image?.layout || 'between')

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

const BackgroundLayout = {
  name: 'BackgroundLayout',
  template: `
    <div class="relative overflow-hidden rounded-md">
      <div class="absolute inset-0 -z-10 bg-center bg-cover" :style="bgStyle"></div>
      <div class="relative">
        <slot />
      </div>
    </div>
  `,
  props: ['imageStyle', 'image'],
  computed: {
    bgStyle() {
      return {
        backgroundImage: `url(${this.image?.url})`,
        backgroundPosition: this.imageStyle.objectPosition,
        filter: this.imageStyle.filter
      }
    }
  }
}

const SideLayout = {
  name: 'SideLayout',
  props: {
    imageFirst: { type: Boolean, default: false },
    small: { type: Boolean, default: false },
    imageStyle: { type: Object, required: true },
    image: { type: Object, required: false }
  },
  template: `
    <div class="grid items-stretch gap-3" :class="gridCols">
      <div v-if="imageFirst" class="overflow-hidden rounded-md">
        <slot name="image" />
      </div>
      <div>
        <slot />
      </div>
      <div v-if="!imageFirst" class="overflow-hidden rounded-md">
        <slot name="image" />
      </div>
    </div>
  `,
  computed: {
    gridCols() {
      if (this.small) return 'grid-cols-[minmax(140px,0.33fr)_1fr]'
      return 'grid-cols-2'
    }
  }
}

const StackLayout = {
  name: 'StackLayout',
  template: `
    <div class="flex flex-col gap-3">
      <div class="overflow-hidden rounded-md">
        <slot name="image" />
      </div>
      <div>
        <slot />
      </div>
    </div>
  `
}

const layoutComponent = computed(() => {
  switch (layout.value) {
    case 'background':
      return {
        extends: BackgroundLayout,
        props: { imageStyle: imageStyle.value, image: props.image }
      }
    case 'left-split':
      return { extends: SideLayout, props: { imageFirst: true, small: false, imageStyle: imageStyle.value, image: props.image } }
    case 'right-split':
      return { extends: SideLayout, props: { imageFirst: false, small: false, imageStyle: imageStyle.value, image: props.image } }
    case 'left-small':
      return { extends: SideLayout, props: { imageFirst: true, small: true, imageStyle: imageStyle.value, image: props.image } }
    case 'right-small':
      return { extends: SideLayout, props: { imageFirst: false, small: true, imageStyle: imageStyle.value, image: props.image } }
    case 'between':
    default:
      return StackLayout
  }
})
</script>

<style scoped>
.grid-cols-[minmax(140px,0.33fr)_1fr] {
  grid-template-columns: minmax(140px, 0.33fr) 1fr;
}
</style>


