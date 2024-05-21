<template>
  <div>
    <div class="flex items-center gap-y-4 flex-wrap-reverse">
      <div class="flex-grow">
        <h3 class="font-semibold text-2xl text-gray-900">
          Connections
        </h3>
        <small class="text-gray-600">Manage your external connections.</small>
      </div>
      <v-button
        color="outline-blue"
        :loading="loading"
        @click="providerModal = true"
      >
        <svg
          class="inline -mt-1 mr-1 h-4 w-4"
          viewBox="0 0 14 14"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M6.99996 1.16699V12.8337M1.16663 7.00033H12.8333"
            stroke="currentColor"
            stroke-width="1.67"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
        Connect new account
      </v-button>
    </div>

    <div
      v-if="loading"
      class="w-full text-blue-500 text-center"
    >
      <Loader class="h-10 w-10 p-5" />
    </div>

    <div class="py-6">
      <SettingsProviderCard
        v-for="provider in providers"
        :key="provider.id"
        :provider="provider"
      />
    </div>

    <!--  Provider modal  -->
    <modal
      :show="providerModal"
      max-width="lg"
      @close="providerModal = false"
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
  </div>
</template>

<script setup>
useOpnSeoMeta({
  title: "Connections",
})

definePageMeta({
  middleware: "auth",
  alias: [
    '/settings/connections/callback/:service'
  ]
})

const router = useRouter()
const route = useRoute()
const alert = useAlert()

const providerModal = ref(false)
const providersStore = useOAuthProvidersStore()
const providers = computed(() => providersStore.getAll)
const loading = computed(() => providersStore.loading)

const services = [
  {
    name: 'google',
    title: 'Google',
    icon: 'mdi:google',
    enabled: true
  },
]

function connect(service) {
  opnFetch(`/settings/providers/connect/${service.name}`, {
    method: 'POST'
  })
    .then((data) => {
      window.location.href = data.url
    })
    .catch((error) => {
      try {
        alert.error(error.data.message)
      } catch (e) {
        alert.error("An error occurred while connecting an account")
      }
    })
}

function handleCallback() {
  const code = route.query.code
  const service = route.params.service

  if(!code || !service) {
    router.push('/settings/connections')

    return
  }

  opnFetch(`/settings/providers/callback/${service}`, {
    method: 'POST',
    params: {
      code
    }
  })
    .then((data) => {
      //
    })
    .catch((error) => {
      try {
        alert.error(error.data.message)
      } catch (e) {
        alert.error("An error occurred while connecting an account")
      }
    })
    .finally(() => {
      router.push('/settings/connections')
    })
}

onMounted(() => {
  handleCallback()

  providersStore.fetchOAuthProviders()
})
</script>
