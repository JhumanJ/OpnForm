<template>
  <div
    class="flex flex-col p-3 rounded-md shadow border-neutral-200 border-[0.5px] transition-colors justify-between w-full cursor-pointer ring-blue-300  relative"
    :class="{'ring bg-blue-100': isSelected, 'bg-white hover:ring-2 hover:bg-blue-50': !isSelected}"
    @click="$emit('select-font')"
  >
    <template v-if="isVisible">
      <link
        :href="getFontUrl"
        rel="stylesheet"
      >
      <div
        class="text-lg mb-3 font-normal"
        :style="{ 'font-family': `${fontName} !important` }"
      >
        The quick brown fox jumped over the lazy dog
      </div>
    </template>
    <div
      v-else
      class="flex flex-wrap gap-2 mb-3"
    >
      <USkeleton
        class="h-5 w-full"
      />
      <USkeleton
        class="h-5 w-3/4"
      />
    </div>

    <div class="text-neutral-400 flex justify-between">
      <p class="text-xs">
        {{ fontName }}
      </p>
    </div>
    <Icon
      v-if="isSelected"
      name="heroicons:check-circle-16-solid"
                class="w-5 h-5 text-blue-500 absolute bottom-4 right-4"
    />
  </div>
</template>

<script setup>
import { defineEmits } from "vue"

const props = defineProps({
  fontName: {
    type: String,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  isVisible: {
    type: Boolean,
    default: false
  }
})

defineEmits(['select-font'])

const getFontUrl = computed(() => {
  const family = props.fontName.replace(/ /g, '+')
  return `https://fonts.googleapis.com/css?family=${family}:wght@400&display=swap`
})
</script>
