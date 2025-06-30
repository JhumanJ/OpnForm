<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">External Connections</h3>
        <p class="mt-1 text-sm text-neutral-500">
          Manage your external connections and integrations.
        </p>
      </div>
      <UButton
        label="Connect Account"
        icon="i-heroicons-plus"
        :loading="loading"
        @click="providerModal = true"
      />
    </div>

    <!-- Providers List -->
    <div class="space-y-4">
      <div v-if="providers.length === 0 && !loading" class="text-center py-12">
        <UIcon 
          name="i-heroicons-link" 
          class="w-12 h-12 text-neutral-400 mx-auto mb-4" 
        />
        <h4 class="text-lg font-medium text-neutral-900 mb-2">
          No connections yet
        </h4>
        <p class="text-neutral-500 mb-4">
          Connect your accounts to enable integrations and streamline your workflow.
        </p>
        <UButton
          label="Connect Your First Account"
          icon="i-heroicons-plus"
          @click="providerModal = true"
        />
      </div>

      <UTable 
        v-if="providers.length > 0"
        v-model:column-pinning="columnPinning"
        :data="providers" 
        :columns="tableColumns"
        :loading="loading"
        class="w-full"
      >
        <template #provider-cell="{ row: { original: item } }">
          <div class="flex items-center gap-3">
            <Icon
              :name="getService(item.provider)?.icon"
              size="24px"
              class="text-blue-500"
            />
            <span class="font-semibold">{{ getService(item.provider)?.name || item.provider }}</span>
          </div>
        </template>

        <template #email-cell="{ row: { original: item } }">
          <span class="text-neutral-600">{{ item.name }}</span>
        </template>

        <template #actions-cell="{ row: { original: item } }">
          <div class="flex justify-end">
            <UButton 
              color="error" 
              variant="soft"
              icon="i-heroicons-trash"
              square
              size="sm"
              @click="disconnectProvider(item)"
            />
          </div>
        </template>
      </UTable>
    </div>

    <!-- Provider Modal -->
    <UsersSettingsConnectionModal
      v-model="providerModal"
      @close="providerModal = false"
    />
  </div>
</template>

<script setup>
import { useOAuthProvidersStore } from '~/stores/oauth_providers'

const providerModal = ref(false)
const providersStore = useOAuthProvidersStore()
const alert = useAlert()

const providers = computed(() => providersStore.getAll)
const loading = computed(() => providersStore.loading)

// Column pinning state
const columnPinning = ref({
  left: [],
  right: ['actions']
})

// Table columns configuration
const tableColumns = [
  {
    id: 'provider',
    accessorKey: 'provider',
    header: 'Service',
    enableSorting: true
  },
  {
    id: 'email',
    accessorKey: 'email',
    header: 'Account',
    enableSorting: true
  },
  {
    id: 'actions',
    header: '',
    enableSorting: false,
    enableHiding: false
  }
]

// Get service information
const getService = (providerName) => {
  return providersStore.getService(providerName)
}

// Disconnect provider
const disconnectProvider = (provider) => {
  alert.confirm("Do you really want to disconnect this account?", () => {
    opnFetch(`/settings/providers/${provider.id}`, {
      method: 'DELETE'
    })
      .then(() => {
        providersStore.remove(provider.id)
        alert.success('Account disconnected successfully')
      })
      .catch((error) => {
        try {
          alert.error(error.data.message)
        } catch {
          alert.error("An error occurred while disconnecting the account")
        }
      })
  })
}

await providersStore.fetchOAuthProviders()
</script> 