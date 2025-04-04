<template>
  <div class="flex flex-1 items-center">
    <div
      v-if="integration"
      class="hidden md:block space-y-1"
    >
      <UBadge
        :label="mentionAsText(integration.data.message)"
        color="gray"
        size="xs"
        class="max-w-[300px] block truncate"
      />
    </div>
  </div>
</template>

<script setup>
import { mentionAsText } from '~/lib/utils.js'

defineProps({
  integration: {
    type: Object,
    required: true,
  },
  form: {
    type: Object,
    required: true,
  }
})
</script> 