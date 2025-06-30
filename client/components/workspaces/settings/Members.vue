<template>
  <div class="space-y-8">
    <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Workspace Members</h3>
        <p class="mt-1 text-sm text-neutral-500">
          Manage your workspace members and their roles.
        </p>
      </div>

      <div class="flex shrink-0 items-center gap-2">
        <UButton
          v-if="!workspace.is_readonly"
          label="Invite User"
          icon="i-heroicons-user-plus-20-solid"
          :loading="loadingUsers"
          @click="appStore.setWorkspaceInviteUserModal(true)"
        />
      </div>
    </div>

    <UTable
      class="w-full"
      :loading="loadingUsers"
      :data="users"
      :columns="tableColumns"
      v-model:column-pinning="columnPinning"
    >
      <template #actions-cell="{ row: { original: item } }">
        <div class="space-x-2 flex justify-center">
          <template v-if="item.type == 'user'">
            <p
              v-if="item.is_current_user"
              class="text-gray-500 text-center text-sm"
            >
              -
            </p>
            <UButtonGroup
              v-else
              size="2xs"
            >
              <UTooltip text="Edit user">
                <UButton
                  icon="i-heroicons-pencil-square"
                  color="primary"
                  variant="outline"
                  size="sm"
                  square
                  @click="editUser(item)"
                />
              </UTooltip>
              <UTooltip text="Remove user">
                <UButton
                  v-if="item.type == 'user'"
                  color="error" 
                  variant="soft"
                  icon="i-heroicons-trash"
                  size="sm"
                  square
                  @click="removeUser(item)"
                />
              </UTooltip>
            </UButtonGroup>
          </template>
          <UButtonGroup
            v-else-if="item.type == 'invitee'"
            size="sm"
          >
            <UTooltip text="Resend Invite">
              <UButton
                icon="i-heroicons-envelope"
                color="neutral"
                variant="outline"
                size="sm"
                @click="resendInvite(item)"
              />
            </UTooltip>
            <UTooltip text="Cancel Invite">
              <UButton
                icon="i-heroicons-x-mark"
                color="error"
                variant="outline"
                size="sm"
                @click="cancelInvite(item)"
              />
            </UTooltip>
          </UButtonGroup>
        </div>
      </template>
    </UTable>

    <UModal
      v-model:open="showEditUserModal"
      @close="showEditUserModal = false"
    >
      <template #header>
        <div class="flex items-center w-full gap-4 px-2">
          <Icon
            name="i-heroicons-pencil-square"
            class="text-blue-500"
            size="20px"
          />
          <h2 class="text-lg font-semibold">
            Edit User Role
          </h2>
        </div>
      </template>
      
      <template #body>
        <div class="px-4">
          <form
            @submit.prevent="updateUserRole"
            @keydown="editUserForm.onKeydown($event)"
          >
            <div>
              <FlatSelectInput
                :form="editUserForm"
                name="role"
                :label="'New Role for '+selectedUser.name"
                :options="[
                  { name: 'User', value: 'user' },
                  { name: 'Admin', value: 'admin' },
                  { name: 'Read Only', value: 'readonly' },
                ]"
                option-key="value"
                display-key="name"
              />
            </div>

            <div class="flex justify-center mt-4">
              <UButton
                type="submit"
                :loading="editUserForm.busy"
                icon="i-heroicons-pencil"
              >
                Update
              </UButton>
            </div>
          </form>
        </div>
      </template>
    </UModal>

  </div>
</template>

<script setup>
const workspacesStore = useWorkspacesStore()
const authStore = useAuthStore()
const appStore = useAppStore()
const alert = useAlert()

const workspace = computed(() => workspacesStore.getCurrent)
const user = computed(() => authStore.user)
const users = ref([])
const loadingUsers = ref(true)
const showEditUserModal = ref(false)
const selectedUser = ref(null)
const editUserForm = useForm({
  role: 'user'
})

// Column pinning state
const columnPinning = ref({
  left: [],
  right: ['actions']
})

// Table columns configuration
const tableColumns = computed(() => {
  return [
    {
      id: 'name',
      accessorKey: 'name',
      header: 'Name'
    },
    {
      id: 'email',
      accessorKey: 'email',
      header: 'Email'
    },
    {
      id: 'role',
      accessorKey: 'role',
      header: 'Role'
    },
    ...(user.value.admin ? [
      {
        id: 'actions',
        header: '',
      }] : [])
  ]
})

onMounted(() => {
  getWorkspaceUsers()
})

// Watch for modal close to refresh users
watch(() => appStore.workspaceInviteUserModal, (newValue, oldValue) => {
  if (oldValue === true && newValue === false) {
    getWorkspaceUsers()
  }
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

const editUser = (user) => {
  selectedUser.value = user
  editUserForm.role = selectedUser.value.pivot.role
  showEditUserModal.value = true
}

const updateUserRole = () => {
  editUserForm.put("/open/workspaces/" + workspacesStore.currentId + "/users/" + selectedUser.value.id + "/update-role").then((data) => {
    alert.success(data.message)
    getWorkspaceUsers()
    showEditUserModal.value = false
  }).catch(() => {
    alert.error("There was an error updating user role")
  })
}

const removeUser = (user) => {
  alert.confirm("Do you really want to remove " + user.name + " from this workspace?", () => {
    loadingUsers.value = true
    opnFetch("/open/workspaces/" + workspacesStore.currentId + "/users/" + user.id + "/remove", {method: "DELETE"}).then(() => {
      alert.success("User successfully removed.")
      getWorkspaceUsers()
    }).catch(() => {
      alert.error("There was an error removing user")
    }).finally(() => {
      loadingUsers.value = false
    })
  })
}

const resendInvite = (user) => {
  alert.confirm("Do you really want to resend invite email to this user?", () => {
    opnFetch("/open/workspaces/" + workspace.value.id + "/invites/" + user.id + "/resend", {method: "POST"}).then(() => {
      alert.success("Invitation resent successfully.")
      getWorkspaceUsers()
    }).catch(err => {
      alert.error(err.response._data?.message)
    })
  })
}

const cancelInvite = (user) => {
  alert.confirm("Do you really want to cancel this user's invitation to this workspace?", () => {
    opnFetch("/open/workspaces/" + workspace.value.id + "/invites/" + user.id + "/cancel", {method: "DELETE"}).then(() => {
      alert.success("Invitation cancelled successfully.")
      getWorkspaceUsers()
    }).catch(err => {
      alert.error(err.response._data?.message)
    })
  })
}

</script> 