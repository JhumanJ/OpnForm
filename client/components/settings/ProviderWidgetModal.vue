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
        @auth-data="handleAuthData"
      />
    </div>
  </modal>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  service: Object
})

const providersStore = useOAuthProvidersStore()
const router = useRouter()
const alert = useAlert()
const emit = defineEmits(['close'])

// Dynamically compute which widget component to load
const widgetComponent = computed(() => {
  if (!props.service?.widget_file) return null
  return resolveComponent(props.service.widget_file)
})

const handleAuthData = async (data) => {
  try {
    if (!data) {
      alert.error('Authentication failed')
      return
    }

    const response = await opnFetch(`/settings/providers/widget-callback/${props.service.name}`, {
      method: 'POST',
      body: data
    })

    if (response.intention) {
      router.push(response.intention)
    } else {
      alert.success('Successfully connected')
      emit('close')
      providersStore.fetchOAuthProviders()
    }
  } catch (error) {
    alert.error(error?.data?.message || 'Failed to authenticate')
    providersStore.fetchOAuthProviders()
  }
}
</script>