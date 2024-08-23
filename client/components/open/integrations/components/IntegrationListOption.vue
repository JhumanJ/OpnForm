<template>
  <UTooltip
    :text="tooltipText"
    :prevent="!unavailable || !tooltipText"
  >
    <div
      v-track.new_integration_click="{ name: integration.id }"
      role="button"
      :class="{
        'hover:bg-blue-50 group cursor-pointer': !unavailable,
        'cursor-not-allowed': integration.coming_soon,
      }"
      class="bg-gray-50 border border-gray-200 rounded-md transition-colors p-4 pb-2 items-center justify-center w-[170px] h-[110px] flex flex-col relative"
      @click="onClick"
    >
      <div class="flex justify-center">
        <div
          class="h-10 w-10 text-gray-500 group-hover:text-blue-500 transition-colors flex items-center"
        >
          <Icon
            :name="integration.icon"
            size="40px"
          />
        </div>
      </div>
      <div class="flex-grow flex items-center">
        <div class="text-gray-400 font-medium text-sm text-center">
          {{ integration.name
          }}<span
            v-if="integration.coming_soon"
            class="text-xs"
          >
            (coming soon)</span>
        </div>
        <Icon
          v-if="integration.is_external"
          class="inline h-4 w-4 ml-1 inline text-gray-500"
          name="heroicons:arrow-top-right-on-square-20-solid"
        />
      </div>
      <pro-tag
        v-if="integration?.is_pro === true"
        class="absolute top-0 right-1"
      />
    </div>
  </UTooltip>
</template>

<script setup>
import { computed } from 'vue'
import { useWorkspacesStore } from '@/stores/workspaces'
const emit = defineEmits(["select"])
const subscriptionModalStore = useSubscriptionModalStore()

const props = defineProps({
  integration: {
    type: Object,
    required: true,
  },
})

const workspacesStore = useWorkspacesStore()
const currentWorkspace = computed(() => workspacesStore.getCurrent)

const unavailable = computed(() => {
  return (
    props.integration.coming_soon || 
    (props.integration.requires_subscription && !currentWorkspace.value.is_pro)
  )
})

const tooltipText = computed(() => {
  if (props.integration.coming_soon) return "This integration is coming soon"
  if (props.integration.requires_subscription && !currentWorkspace.value.is_pro )
    return "You need a subscription to use this integration."
  return null
})

const onClick = () => {
  if (props.integration.coming_soon) return
  if (props.integration.requires_subscription && !currentWorkspace.value.is_pro ) {
    subscriptionModalStore.setModalContent(
      'Upgrade today to use this integration',
      `Upgrade your account to use "${props.integration.name}" and unlock all of our Pro features.`
    )
    subscriptionModalStore.openModal()
    return
  }
  emit("select", props.integration.id)
}
</script>