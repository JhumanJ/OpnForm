<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-md' }"
  >
    <template #header>
      <div class="flex items-center justify-center">
        <Icon
          :name="service.icon"
          class="h-8 w-8 text-gray-500"
        />
        <span class="ml-2 text-gray-700 font-medium">{{ service.title }}</span>
      </div>
    </template>

    <template #body>
      <div class="flex items-center justify-center">
        <!-- Dynamic component loading based on service -->
        <component
          :is="widgetComponent"
          v-if="widgetComponent"
          :service="service"
          @auth-data="handleAuthData"
        />
      </div>
    </template>

    <template #footer>
      <UButton
        color="neutral"
        variant="outline"
        @click="closeModal"
      >
        Close
      </UButton>
    </template>
  </UModal>
</template>

<script setup>
import { oauthApi } from "~/api"

const props = defineProps({
  show: Boolean,
  service: Object
})

const providersStore = useOAuthProvidersStore()
const router = useRouter()
const alert = useAlert()
const emit = defineEmits(['close'])

// Modal state
const isOpen = computed({
  get: () => props.show,
  set: (value) => {
    if (!value) {
      emit('close')
    }
  }
})

const closeModal = () => {
  isOpen.value = false
}

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

    const response = await oauthApi.widgetCallback(props.service.name, data)

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