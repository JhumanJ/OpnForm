<template>
  <ClientOnly v-if="clientOnly">
    <slot />
    <template #fallback>
      <slot name="fallback" />
    </template>
  </ClientOnly>
  <template v-else>
    <slot />
  </template>
</template>

<script setup>
defineProps({
  clientOnly: {
    type: Boolean,
    default: false
  }
})
</script> 