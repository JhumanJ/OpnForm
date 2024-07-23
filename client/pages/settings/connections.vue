<template>
  <div>
    <div class="flex items-center gap-y-4 flex-wrap-reverse">
      <div class="flex-grow">
        <h3 class="font-semibold text-2xl text-gray-900">
          Connections
        </h3>
        <small class="text-gray-600">Manage your external connections.</small>
      </div>
      <UButton
        label="Connect new account"
        icon="i-heroicons-plus"
        :loading="loading"
        @click="providerModal = true"
      />
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

    <SettingsProviderModal
      :show="providerModal"
      @close="providerModal = false"
    />
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
      if(!data.intention) {
        router.push('/settings/connections')
      }
      else {
        router.push(data.intention)
      }
    })
    .catch((error) => {
      try {
        alert.error(error.data.message)
      } catch (e) {
        alert.error("An error occurred while connecting an account")
      }

      router.push('/settings/connections')
    })
}

onMounted(() => {
  handleCallback()

  providersStore.fetchOAuthProviders()
})
</script>
