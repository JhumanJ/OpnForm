<template>
  <modal
    :show="show"
    max-width="lg"
    @close="emit('close')"
  >
    <template #icon>
      <svg
        class="w-8 h-8"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </template>

    <template #title>
      Connect account
    </template>

    <div class="px-4">
      <div
        v-for="service in services"
        :key="service.name"
        role="button"
        class="bg-gray-50 border border-gray-200 rounded-md transition-colors p-4 pb-2 items-center justify-center w-[170px] h-[110px] flex flex-col relative"
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
  </modal>
</template>

<script setup>
defineProps({
  show: Boolean
})

const emit = defineEmits(['close'])

const providersStore = useOAuthProvidersStore()
const services = computed(() => providersStore.services)

function connect(service) {
  providersStore.connect(service.name)
}
</script>
