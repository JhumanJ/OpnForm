<template>
  <UModal
    v-model:open="isOpen"
    @close="closeModal"
  >
    <template #header>
      <div class="flex items-center w-full gap-4 px-2">
        <h2 class="font-semibold">
          Connect account
        </h2>
      </div>
    </template>

    <template #body>
      <div v-if="loading">
        <Loader class="h-6 w-6 mx-auto" />
      </div>
      <div
        v-else
        class="flex"
      >
        <div
          v-for="service in services"
          :key="service.name"
          role="button"
          class="mr-2 bg-gray-50 border border-gray-200 rounded-md transition-colors p-4 pb-2 items-center justify-center w-[170px] h-[110px] flex flex-col relative"
          :class="{
            'hover:bg-blue-50 group cursor-pointer': service.enabled,
            'cursor-not-allowed': !service.enabled,
          }"
          @click="connect(service)"
        >
          <div class="flex justify-center">
            <div class="h-10 w-10 text-gray-500 group-hover:text-blue-500 transition-colors flex items-center">
              <Icon
                :name="service.icon"
                class=""
                size="40px"
              />
            </div>
          </div>

          <div class="flex-grow flex items-center">
            <div class="text-gray-400 font-medium text-sm text-center">
              {{ service.title }}
            </div>
          </div>
        </div>
      </div>
    </template>
  </UModal>

  <!-- Add widget modal -->
  <UsersSettingsProviderWidgetModal
    v-if="showWidgetModal"
    :show="showWidgetModal"
    :service="selectedService"
    @close="showWidgetModal = false"
  />
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const oAuth = useOAuth()
const services = oAuth.services
const loading = ref(false)
const showWidgetModal = ref(false)
const selectedService = ref(null)

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('close', value)
})

// Methods
const closeModal = () => {
  isOpen.value = false
}

function connect(service) {
  if (!service.enabled) {
    useAlert().error('This service is not enabled. Please contact support.')
    return
  }

  if (service.auth_type === 'widget') {
    emit('close')
    selectedService.value = service
    showWidgetModal.value = true
  } else {
    // Use existing redirect flow
    loading.value = true
    oAuth.connect(service.name)
  }
}
</script>