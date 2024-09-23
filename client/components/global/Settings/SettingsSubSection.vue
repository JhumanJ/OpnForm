<template>
  <div class="mb-8 flex">
    <div class="w-1/3 pr-8">
      <h3 class="text-lg font-semibold mb-1">
        {{ title }}
      </h3>
      <p class="text-sm text-gray-500">
        {{ description }}
      </p>
    </div>
    <div class="w-2/3">
      <UCard class="divide-y divide-gray-200">
        <slot />
      </UCard>
    </div>
  </div>
</template>
  
  <script setup>
  defineProps({
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      required: true
    }
  })
  </script>