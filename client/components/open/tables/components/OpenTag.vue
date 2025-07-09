<template>
  <UBadge
    :id="opt"
    class="mb-1"
    size="sm"
    color="neutral"
    variant="soft"
  >
    {{ opt }}
  </UBadge>
</template>

<script setup>
defineProps({
  opt: {
    type: String,
    required: true,
  },
})
</script>


