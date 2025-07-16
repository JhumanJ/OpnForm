<template>
  <UBadge
    :label="displayLabel"
    :color="badgeColor"
    variant="subtle"
    class="mx-auto"
    size="sm"
  />
</template>

<script setup>
const props = defineProps({
  value: {
    type: String,
    required: true,
  },
})

const displayLabel = computed(() => {
  return props.value === 'partial' ? 'In Progress' : 'Submitted'
})

const badgeColor = computed(() => {
  return props.value === 'partial' ? 'warning' : 'success'
})
</script> 