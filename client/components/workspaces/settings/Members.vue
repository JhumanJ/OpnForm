<template>
  <div class="space-y-8">
    <!-- Profile Information Section -->
    <div class="space-y-4">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Workspace Members</h3>
        <p class="text-sm text-neutral-500 mt-1">
          Manage your workspace members and their roles.
        </p>
      </div>

      <UTable
        class="-mx-4 border-y mt-4"
        :loading="loadingUsers"
        :data="users"
        :columns="columns"
      >
        <template
          v-if="isWorkspaceAdmin"
          #actions-cell="{ row }"
        >
          <div class="space-x-2 flex justify-center">
            <template v-if="row.original.type == 'user'">
              <p
                v-if="row.original.is_current_user"
                class="text-gray-500 text-center text-sm"
              >
                -
              </p>
              <UButtonGroup
                v-else
                size="2xs"
              >
                <UTooltip
                  text="Edit user"
                >
                  <UButton
                    icon="i-heroicons-pencil"
                    color="neutral"
                    class="hover:text-blue-500"
                    square
                    @click="editUser(row.original)"
                  />
                </UTooltip>
                <UTooltip
                  text="Remove user"
                >
                  <UButton
                    v-if="row.original.type == 'user'"
                    icon="i-heroicons-trash"
                    color="neutral"
                    class="hover:text-red-500"
                    square
                    @click="removeUser(row.original)"
                  />
                </UTooltip>
              </UButtonGroup>
            </template>
            <UButtonGroup
              v-else-if="row.original.type == 'invitee'"
              size="2xs"
            >
              <UTooltip
                text="Resend Invite"
              >
                <UButton
                  icon="i-heroicons-envelope"
                  color="neutral"
                  class="hover:text-blue-500"
                  square
                  @click="resendInvite(row.original)"
                />
              </UTooltip>
              <UTooltip
                text="Cancel Invite"
              >
                <UButton
                  icon="i-heroicons-trash"
                  color="neutral"
                  class="hover:text-red-500"
                  square
                  @click="cancelInvite(row.original)"
                />
              </UTooltip>
            </UButtonGroup>
          </div>
        </template>
      </UTable>
    </div>
  </div>
</template>

<script setup>
const workspacesStore = useWorkspacesStore()
const authStore = useAuthStore()

const users = ref([])
const loadingUsers = ref(true)

const isWorkspaceAdmin = computed(() => {
  if (!users.value) return false
  const user = users.value.find((user) => user.id === authStore.user.id)
  return user && user.pivot.role === "admin"
})

const columns = computed(() => {
  return [
    {accessorKey: 'name', header: 'Name'},
    {accessorKey: 'email', header: 'Email'},
    {accessorKey: 'role', header: 'Role'},
    ...(isWorkspaceAdmin.value ? [{id: 'actions', header: 'Action'}] : [])
  ]
})

onMounted(() => {
  getWorkspaceUsers()
})

const getWorkspaceUsers = async () => {
  loadingUsers.value = true
  let data = await workspacesStore.getWorkspaceUsers()
  data = data.map(d => {
    return {
      ...d,
      id: d.id,
      is_current_user: d.id === authStore.user.id,
      name: d.name,
      email: d.email,
      status: 'accepted',
      role: d.pivot.role,
      type: 'user'
    }
  })
  let invites = await workspacesStore.getWorkspaceInvites()
  invites = invites.filter(i => i.status !== 'accepted').map(i => {
    return {
      ...i,
      name: 'Invitee',
      email: i.email,
      status: i.status,
      type: 'invitee'
    }
  })
  users.value = [...data, ...invites]
  loadingUsers.value = false
}

</script> 