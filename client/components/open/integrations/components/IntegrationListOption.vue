<template>
  <UTooltip :text="tooltipText" :prevent="!unavailable">
    <div role="button" @click="onClick"
         v-track.new_integration_click="{ name: integration.id }"
         :class="{'hover:bg-blue-50 group cursor-pointer': !unavailable, 'cursor-not-allowed': unavailable}"
         class="bg-gray-50 border border-gray-200 rounded-md transition-colors p-4 pb-2 items-center justify-center w-[170px] h-[110px] flex flex-col relative">
      <div class="flex justify-center">
        <div class="h-10 w-10 text-gray-500 group-hover:text-blue-500 transition-colors flex items-center">
          <Icon :name="integration.icon" size="40px"/>
        </div>
      </div>
      <div class="flex-grow flex items-center">
        <div class="text-gray-400 font-medium text-sm text-center">
          {{ integration.name }}<span class="text-xs" v-if="integration.coming_soon"> (coming soon)</span>
        </div>
      </div>
      <pro-tag v-if="integration?.is_pro === true" class="absolute top-0 right-1"/>
    </div>
  </UTooltip>
</template>

<script setup>

const emit = defineEmits(['select'])

const props = defineProps({
  integration: {
    type: Object,
    required: true
  }
})

const unavailable = computed(() => {
  return props.integration.coming_soon || props.integration.requires_subscription
})

const tooltipText = computed(() => {
  if (props.integration.coming_soon) return 'This integration is coming soon'
  if (props.integration.requires_subscription) return 'You need a subscription to use this integration.'
  return ''
})

const onClick = () => {
  if (props.integration.coming_soon || props.integration.requires_subscription) return
  emit('select', props.integration.id)
}


</script>
