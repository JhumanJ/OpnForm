<template>
  <div
    v-if="isActive"
    class="settings-section"
  >
    <h3 class="text-xl font-medium mb-1">
      {{ name }}
    </h3>
    <slot />
  </div>
</template>
  
<script setup>
import { inject, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  name: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  }
})

const activeSection = inject('activeSection', ref(''))
const registerSection = inject('registerSection', () => {})
const unregisterSection = inject('unregisterSection', () => {})

const isActive = computed(() => activeSection.value === props.name)

onMounted(() => {
  registerSection(props.name, props.icon)
})

onBeforeUnmount(() => {
  unregisterSection(props.name)
})
</script>