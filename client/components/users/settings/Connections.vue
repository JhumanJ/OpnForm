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
        :loading="isLoading"
        @click="providerModal = true"
      />
    </div>

    <!-- Providers List -->
    <div class="space-y-4">
      <div v-if="providers.length === 0 && !isLoading" class="text-center py-12">
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
        :loading="isLoading"
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
const providerModal = ref(false)
const oAuth = useOAuth()
const alert = useAlert()

const { data: providersData, isLoading, refetch } = oAuth.providers()
const providers = computed(() => providersData.value || [])

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
  return oAuth.getService(providerName)
}

// Disconnect provider mutation
const removeMutation = oAuth.remove()

// Disconnect provider
const disconnectProvider = (provider) => {
  alert.confirm("Do you really want to disconnect this account?", () => {
    removeMutation.mutateAsync(provider.id).then(() => {
      alert.success('Account disconnected successfully')
      refetch()
    }).catch((error) => {
      try {
        alert.error(error.data.message)
      } catch {
        alert.error("An error occurred while disconnecting the account")
      }
    })
  })
}

// Fetch providers on mount
await oAuth.fetchOAuthProviders()
</script> 