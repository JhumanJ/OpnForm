<template>
  <modal
    :show="show"
    max-width="md"
    @close="emit('close')"
  >
    <template #title>
      <div class="flex items-center justify-center mb-4">
        <Icon
          :name="service.icon"
          class="h-8 w-8 text-gray-500"
        />
        <span class="ml-2 text-gray-700 font-medium">{{ service.title }}</span>
      </div>
    </template>

    <div class="p-4 flex items-center justify-center">
      <!-- Dynamic component loading based on service -->
      <component
        :is="widgetComponent"
        v-if="widgetComponent"
        :service="service"
      />
    </div>
  </modal>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  service: Object
})

const emit = defineEmits(['close'])

// Dynamically compute which widget component to load
const widgetComponent = computed(() => {
  if (!props.service?.widget_file) return null
  return resolveComponent(props.service.widget_file)
})
</script>