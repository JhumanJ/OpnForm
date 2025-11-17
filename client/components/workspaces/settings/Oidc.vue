<template>
  <div class="space-y-4">
    <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">SSO Settings</h3>
        <p class="mt-1 text-sm text-neutral-500">
          Configure OpenID Connect (OIDC) single sign-on for your workspace.
        </p>
      </div>

      <UButton
        v-if="canManageConnections"
        label="Add Connection"
        icon="i-heroicons-plus"
        @click="showCreateModal = true"
      />
    </div>

    <!-- Connections List -->
    <div v-if="connectionsData && connectionsData.length > 0" class="space-y-3">
      <p class="text-sm text-neutral-500 max-w-xl">
        Each connection can be tied to one verified email domain, which we use to route incoming users to the
        correct workspace when they start login. Manage multiple clients from here and toggle them on or off without
        losing their configuration details.
      </p>
      <div class="grid gap-3 sm:grid-cols-2">
        <OidcConnectionCard
        v-for="connection in connectionsData"
        :key="connection.id"
          :connection="connection"
          :can-edit="canManageConnections"
          @edit="editConnection"
          @delete="deleteConnection"
          />
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!isConnectionsLoading" class="text-center py-12">
      <UIcon 
        name="i-heroicons-key" 
        class="w-12 h-12 text-neutral-400 mx-auto mb-4" 
      />
      <h4 class="text-lg font-medium text-neutral-900 mb-2">
        No OIDC connections yet
      </h4>
      <p class="text-neutral-500 mb-4">
        Configure your first OIDC connection to enable single sign-on for your workspace.
      </p>
      <UButton
        v-if="canManageConnections"
        label="Add Your First Connection"
        icon="i-heroicons-plus"
        @click="showCreateModal = true"
      />
    </div>

    <!-- Create/Edit Modal -->
    <OidcConnectionModal
      :model-value="showCreateModal"
      :connection="editingConnection"
            :form="connectionForm"
      :is-busy="connectionForm.busy"
      @update:model-value="showCreateModal = $event"
      @save="saveConnection"
      @cancel="cancelEdit"
    />
  </div>
</template>

<script setup>
import { useOidcConnections } from '~/composables/query/useOidcConnections'
import OidcConnectionCard from './OidcConnectionCard.vue'
import OidcConnectionModal from './OidcConnectionModal.vue'

const { current: workspace } = useCurrentWorkspace()
const alert = useAlert()

const workspaceId = computed(() => workspace.value?.id)

const { connections, create, update, remove } = useOidcConnections(workspaceId)

const { data: connectionsData, isLoading: isConnectionsLoading } = connections()

const canManageConnections = computed(() => !!workspace.value && workspace.value.is_admin)

const showCreateModal = ref(false)
const editingConnection = ref(null)

const connectionForm = useForm({
  name: '',
  slug: '',
  issuer: '',
  client_id: '',
  client_secret: '',
  domain: '',
  enabled: true,
  options: {
    field_mappings: {
      email: '',
      name: ''
    },
    group_role_mappings: []
  }
})

const createMutation = create({
  onSuccess: () => {
    alert.success('OIDC connection created successfully')
    showCreateModal.value = false
    connectionForm.reset()
  },
})

const updateMutation = update({
  onSuccess: () => {
    alert.success('OIDC connection updated successfully')
    showCreateModal.value = false
    cancelEdit()
  },
})

const deleteMutation = remove({
  onSuccess: () => {
    alert.success('OIDC connection deleted successfully')
  },
  onError: (error) => {
    alert.error(error.response?._data?.message ?? 'Failed to delete connection')
  },
})

// Create wrapper mutations that work with form.mutate()
const createMutationWrapper = {
  mutateAsync: (formData) => {
    return createMutation.mutateAsync({
      ...formData,
      workspace_id: workspaceId.value,
    })
  },
}

const updateMutationWrapper = {
  mutateAsync: (formData) => {
    return updateMutation.mutateAsync({
      connectionId: editingConnection.value.id,
      data: formData,
    })
  },
}

const saveConnection = () => {
  if (editingConnection.value) {
    connectionForm.mutate(updateMutationWrapper)
      .then(() => {
        // Success handled in mutation onSuccess
      })
      .catch((error) => {
        // Form handles validation errors automatically
        if (error.response?.status !== 422) {
          alert.error(error.response?._data?.message ?? 'Failed to update connection')
        }
    })
  } else {
    connectionForm.mutate(createMutationWrapper)
      .then(() => {
        // Success handled in mutation onSuccess
      })
      .catch((error) => {
        // Form handles validation errors automatically
        if (error.response?.status !== 422) {
          alert.error(error.response?._data?.message ?? 'Failed to create connection')
        }
    })
  }
}

const editConnection = (connection) => {
  editingConnection.value = connection
  connectionForm.resetAndFill({
    name: connection.name,
    slug: connection.slug,
    issuer: connection.issuer,
    client_id: connection.client_id,
    client_secret: '', // Don't pre-fill secret
    enabled: connection.enabled,
    domain: connection.domain ?? '',
    options: {
      field_mappings: {
        email: connection.options?.field_mappings?.email ?? '',
        name: connection.options?.field_mappings?.name ?? ''
      },
      group_role_mappings: connection.options?.group_role_mappings ?? []
    }
  })
  showCreateModal.value = true
}

const deleteConnection = (connection) => {
  alert.confirm(
    `Are you sure you want to delete "${connection.name}"?`,
    () => {
      deleteMutation.mutate(connection.id)
    }
  )
}

const cancelEdit = () => {
  editingConnection.value = null
  connectionForm.reset()
  showCreateModal.value = false
}
</script>

