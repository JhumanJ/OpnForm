<template>
  <div class="settings-modal-page">
    <Suspense v-if="isActive">
      <slot />
      <template #fallback>
        <!-- Loading skeleton while component loads -->
        <div class="space-y-4">
          <USkeleton class="h-8 w-1/3" />
          <USkeleton class="h-4 w-full" />
          <USkeleton class="h-4 w-2/3" />
          <div class="space-y-3 mt-6">
            <USkeleton class="h-10 w-full" />
            <USkeleton class="h-10 w-full" />
            <USkeleton class="h-24 w-full" />
          </div>
        </div>
      </template>
    </Suspense>
  </div>
</template>

<script setup>
import { inject, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  }
})

const activeItem = inject('activeModalItem', ref(''))
const registerModalPage = inject('registerModalPage', () => {})
const unregisterModalPage = inject('unregisterModalPage', () => {})

const isActive = computed(() => activeItem.value === props.id)

onMounted(() => {
  registerModalPage(props.id, props.label, props.icon)
})

onBeforeUnmount(() => {
  unregisterModalPage(props.id)
})
</script> 