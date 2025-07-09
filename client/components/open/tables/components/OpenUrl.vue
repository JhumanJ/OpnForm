<template>
  <a
    v-if="value"
    class="text-sm text-neutral-900 underline decoration-neutral-300"
    :href="value"
    target="_blank"
    rel="nofollow"
  >{{ value }}</a>
</template>

<script setup>
defineProps({
  value: {
    type: String,
    required: false,
    default: null,
  },
})
</script>
