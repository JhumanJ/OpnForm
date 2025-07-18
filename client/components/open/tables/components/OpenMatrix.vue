<template>
  <span
    v-if="value"
    class="flex flex-wrap gap-1"
  >
    <template v-if="matrixData">
      <UBadge
        v-for="(data) in matrixData"
        :key="data.label+data.value"
        size="sm"
        color="neutral"
        variant="soft"
      >
        <span class="border-r pr-1 border-neutral-300">{{ data.label }}</span>
        <span>{{ data.value }}</span>
      </UBadge>

    </template>
    <open-tag
      v-else
      :opt="value"
    />
  </span>
</template>

<script setup>
import OpenTag from "./OpenTag.vue"

const props = defineProps({
  value: {
    type: Object,
  },
})

const matrixData = computed(() => {
  if (typeof props.value === "object" && props.value !== null) {
    return Object.entries(props.value).map(([label, value]) => {
        return { label, value }
    })
  }
  return null
})
</script>
