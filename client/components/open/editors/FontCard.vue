<template>
  <div
    class="flex flex-col p-3 rounded-md shadow border-gray-200 border-[0.5px] justify-between w-full cursor-pointer hover:ring ring-blue-300"
    :class="{'ring': isSelected }"
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
    <USkeleton
      v-else
      class="mb-3 h-6 w-full"
    />

    <div class="text-gray-400 flex justify-between">
      <div>{{ fontName }}</div>
      <Icon
        v-if="isSelected"
        name="heroicons:check-circle-16-solid"
        class="w-5 h-5 text-nt-blue"
      />
    </div>
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

const emit = defineEmits(['select-font'])

const getFontUrl = computed(() => {
  const family = props.fontName.replace(/ /g, '+')
  return `https://fonts.googleapis.com/css?family=${family}:wght@400&display=swap`
})
</script>
