<template>
  <div
    :class="size"
    class="rounded-full text-xs truncate bg-neutral-200 text-center flex items-center justify-center overflow-hidden shrink-0"
  >
  <img
    v-if="isUrl(workspace.icon)"
    :src="workspace.icon"
    :alt="`${workspace.name} icon`"
          class="flex-shrink-0 rounded-sm"
    :class="size"
  >
    <p v-else
      class="font-semibold text-neutral-500"
      v-text="displayText"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  workspace: {
    type: Object,
    required: true,
  },
  size: {
    type: String,
    default: 'h-6 w-6',
  },
})

const isUrl = (str) => {
  try {
    new URL(str)
  }
  catch {
    return false
  }
  return true
}

const displayText = computed(() => {
  if (props.workspace.icon) {
    return props.workspace.icon
  }
  // Fallback to first letter of workspace name, capitalized
  return props.workspace.name ? props.workspace.name.charAt(0).toUpperCase() : ''
})
</script>
