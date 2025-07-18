<template>
  <div
    :class="[size, colorClasses]"
    class="rounded-full text-xs truncate text-center flex items-center justify-center overflow-hidden shrink-0"
  >
  <img
    v-if="isUrl(workspace.icon)"
    :src="workspace.icon"
    :alt="`${workspace.name} icon`"
          class="flex-shrink-0 rounded-sm"
    :class="size"
  >
    <p v-else
      class="font-semibold"
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

const colorClasses = computed(() => {
    if (!props.workspace?.id) {
        return 'bg-neutral-200 text-neutral-500'
    }

    const colors = [
        { bg: 'bg-red-200', text: 'text-red-600' },
        { bg: 'bg-orange-200', text: 'text-orange-600' },
        { bg: 'bg-amber-200', text: 'text-amber-600' },
        { bg: 'bg-yellow-200', text: 'text-yellow-600' },
        { bg: 'bg-lime-200', text: 'text-lime-600' },
        { bg: 'bg-green-200', text: 'text-green-600' },
        { bg: 'bg-emerald-200', text: 'text-emerald-600' },
        { bg: 'bg-teal-200', text: 'text-teal-600' },
        { bg: 'bg-cyan-200', text: 'text-cyan-600' },
        { bg: 'bg-sky-200', text: 'text-sky-600' },
        { bg: 'bg-blue-200', text: 'text-blue-600' },
        { bg: 'bg-indigo-200', text: 'text-indigo-600' },
        { bg: 'bg-violet-200', text: 'text-violet-600' },
        { bg: 'bg-purple-200', text: 'text-purple-600' },
        { bg: 'bg-fuchsia-200', text: 'text-fuchsia-600' },
        { bg: 'bg-pink-200', text: 'text-pink-600' },
        { bg: 'bg-rose-200', text: 'text-rose-600' },
    ]

    const colorPair = colors[props.workspace.id % colors.length]

    return `${colorPair.bg} ${colorPair.text}`
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
