<template>
  <div
    class="text-gray-500 border shadow rounded-md p-5 mt-4 relative flex items-center"
  >
    <div class="flex-grow flex items-center">
      <div
        class="mr-4 text-blue-500"
      >
        <Icon
          :name="service?.icon"
          size="32px"
        />
      </div>
      <div>
        <div class="flex space-x-3 font-semibold mr-2">
          {{ provider.name }}
        </div>

        <div>
          {{ provider.email }}
        </div>
      </div>
    </div>

    <div class="flex items-center gap-4">
      <dropdown
        class="inline"
      >
        <template #trigger="{ toggle }">
          <v-button
            color="white"
            @click="toggle"
          >
            <svg
              class="w-4 h-4 inline -mt-1"
              viewBox="0 0 16 4"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M8.00016 2.83366C8.4604 2.83366 8.8335 2.46056 8.8335 2.00033C8.8335 1.54009 8.4604 1.16699 8.00016 1.16699C7.53993 1.16699 7.16683 1.54009 7.16683 2.00033C7.16683 2.46056 7.53993 2.83366 8.00016 2.83366Z"
                stroke="#344054"
                stroke-width="1.66667"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <path
                d="M13.8335 2.83366C14.2937 2.83366 14.6668 2.46056 14.6668 2.00033C14.6668 1.54009 14.2937 1.16699 13.8335 1.16699C13.3733 1.16699 13.0002 1.54009 13.0002 2.00033C13.0002 2.46056 13.3733 2.83366 13.8335 2.83366Z"
                stroke="#344054"
                stroke-width="1.66667"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
              <path
                d="M2.16683 2.83366C2.62707 2.83366 3.00016 2.46056 3.00016 2.00033C3.00016 1.54009 2.62707 1.16699 2.16683 1.16699C1.70659 1.16699 1.3335 1.54009 1.3335 2.00033C1.3335 2.46056 1.70659 2.83366 2.16683 2.83366Z"
                stroke="#344054"
                stroke-width="1.66667"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </v-button>
        </template>
        <a
          v-track.delete_provider_click="{
            provider_id: provider.id,
          }"
          href="#"
          class="flex px-4 py-2 text-md text-red-600 hover:bg-red-50 hover:no-underline items-center"
          @click.prevent="disconnect"
        >
          <Icon
            name="heroicons:trash"
            class="w-5 h-5 mr-2"
          />

          Disconnect
        </a>
      </dropdown>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  provider: Object
})

const providersStore = useOAuthProvidersStore()
const service = computed(() => providersStore.getService(props.provider?.provider))
const alert = useAlert()

function disconnect() {
  alert.confirm("Do you really want to disconnect this account?", () => {
  opnFetch(`/settings/providers/${props.provider.id}`, {
    method: 'DELETE'
  })
    .then(() => {
      providersStore.remove(props.provider.id)
    })
    .catch((error) => {
      try {
        alert.error(error.data.message)
      } catch {
        alert.error("An error occurred while disconnecting an account")
      }
    })
  })
}
</script>
