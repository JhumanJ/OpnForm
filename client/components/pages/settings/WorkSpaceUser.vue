<template>
  <div class="border rounded-md p-4">
    <div class="flex items-center justify-between">
      <h4 class="font-semibold">
        Workspace Members
      </h4>
      <UButton
        label="Invite User"
        icon="i-heroicons-user-plus-20-solid"
        :loading="loading"
        @click="userInviteModal = true"
      />
    </div>
    <!--  User invite modal  -->
    <modal
      :show="userInviteModal"
      max-width="lg"
      @close="userInviteModal = false"
    >
      <h4 class="mb-4 font-medium">
        Invite a new user and collaborate on building forms
      </h4>

      <template v-if="paidPlansEnabled && !hasActiveLicense">
        <UAlert
          v-if="workspace.is_pro"
          icon="i-heroicons-credit-card"
          color="primary"
          variant="subtle"
          title="This is a billable event."
        >
          <template #description>
            You will be charged $6/month for each user you invite to this workspace. More details on the
            <NuxtLink
              target="_blank"
              class="underline"
              :to="{name:'settings-billing'}"
            >
              billing
            </NuxtLink>
            and
            <NuxtLink
              target="_blank"
              class="underline"
              :to="{name:'pricing'}"
            >
              pricing
            </NuxtLink>
            page.
          </template>
        </UAlert>
        <UAlert
          v-else
          icon="i-heroicons-user-group-20-solid"
          color="orange"
          variant="subtle"
          title="Pro plan required"
        >
          <template #description>
            You need a Pro plan to invite new users on OpnForm. Please upgrade on our
            <NuxtLink
              target="_blank"
              class="underline"
              :to="{name:'pricing'}"
            >
              pricing
            </NuxtLink>
            page.
          </template>
        </UAlert>
      </template>

      <AddUserToWorkspace
        :disabled="!canInviteUser"
        :is-workspace-admin="isWorkspaceAdmin"
        @fetch-users="getWorkspaceUsers"
      />
    </modal>
    <UTable
      class="-mx-4 border-y mt-4"
      :loading="loadingUsers"
      :rows="users"
      :columns="columns"
    >
      <template
        v-if="isWorkspaceAdmin"
        #actions-data="{ row, index }"
      >
        <div class="space-x-2 flex justify-center">
          <template v-if="row.type == 'user'">
            <p
              v-if="row.is_current_user"
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
                  color="gray"
                  class="hover:text-blue-500"
                  square
                  @click="editUser(index)"
                />
              </UTooltip>
              <UTooltip
                text="Remove user"
              >
                <UButton
                  v-if="row.type == 'user'"
                  icon="i-heroicons-trash"
                  color="gray"
                  class="hover:text-red-500"
                  square
                  @click="removeUser(index)"
                />
              </UTooltip>
            </UButtonGroup>
          </template>
          <UButtonGroup
            v-else-if="row.type == 'invitee'"
            size="2xs"
          >
            <UTooltip
              text="Resend Invite"
            >
              <UButton
                icon="i-heroicons-envelope"
                color="gray"
                class="hover:text-blue-500"
                square
                @click="resendInvite(index)"
              />
            </UTooltip>
            <UTooltip
              text="Cancel Invite"
            >
              <UButton
                icon="i-heroicons-trash"
                color="gray"
                class="hover:text-red-500"
                square
                @click="cancelInvite(index)"
              />
            </UTooltip>
          </UButtonGroup>
        </div>
      </template>
    </UTable>

    <EditWorkSpaceUser
      :user="selectedUser"
      :show-edit-user-modal="showEditUserModal"
      @close="showEditUserModal = false"
      @fetch-users="getWorkspaceUsers"
    />

    <div class="flex gap-2 mt-4">
      <UButton
        v-if="users.length > 1"
        color="gray"
        icon="i-heroicons-arrow-left-start-on-rectangle-20-solid"
        :loading="leaveWorkspaceLoadingState"
        @click="leaveWorkSpace(workspace.id)"
      >
        Leave Workspace
      </UButton>

      <UButton
        v-if="isWorkspaceAdmin && users.length == 1"
        icon="i-heroicons-trash"
        color="gray"
        :loading="loading"
        @click="deleteWorkspace(workspace.id)"
      >
        Remove workspace
      </UButton>
    </div>
  </div>
</template>

<script setup>
const workspacesStore = useWorkspacesStore()
const authStore = useAuthStore()

const workspace = computed(() => workspacesStore.getCurrent)
const loading = computed(() => workspacesStore.loading)
const workspaces = computed(() => workspacesStore.getAll)

const users = ref([])
const loadingUsers = ref(true)
const leaveWorkspaceLoadingState = ref(false)

const userInviteModal = ref(false)
const showEditUserModal = ref(false)
const selectedUser = ref(null)
const userNewRole = ref("")

const paidPlansEnabled = computed(() => useRuntimeConfig().public.paidPlansEnabled)
const canInviteUser = computed(() => {
  return paidPlansEnabled.value ? workspace.value.is_pro : true
})
const hasActiveLicense = computed(() => authStore.has_active_license)

onMounted(() => {
  getWorkspaceUsers()
})

const getWorkspaceUsers = async () => {
  userInviteModal.value = false
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

const isWorkspaceAdmin = computed(() => {
  if (!users.value) return false
  const user = users.value.find((user) => user.id === authStore.user.id)
  return user && user.pivot.role === "admin"
})

const columns = computed(() => {
  return [
    {key: 'name', label: 'Name'},
    {key: 'email', label: 'Email'},
    {key: 'role', label: 'Role'},
    ...(isWorkspaceAdmin.value ? [{key: 'actions', label: 'Action', class: 'text-center'}] : [])
  ]
})


const editUser = (row) => {
  selectedUser.value = users.value[row]
  userNewRole.value = selectedUser.value.pivot.role
  showEditUserModal.value = true
}


const removeUser = (index) => {
  const user = users.value[index]
  useAlert().confirm(
    "Do you really want to remove " + user.name + " from this workspace?",
    () => {
      loadingUsers.value = true
      opnFetch(
        "/open/workspaces/" + workspacesStore.currentId + "/users/" + user.id + "/remove",
        {
          method: "DELETE",
        },
        {showSuccess: false},
      ).then(() => {
        useAlert().success("User successfully removed.")
        getWorkspaceUsers()
      }).catch((error) => {
        useAlert().error("There was an error removing user")
      }).finally(() => {
        loadingUsers.value = false
      })
    },
  )
}

const deleteWorkspace = (workspaceId) => {
  if (workspaces.length <= 1) {
    useAlert().error("You cannot delete your only workspace.")
    return
  }
  useAlert().confirm(
    "Do you really want to delete this workspace? All forms created in this workspace will be removed.",
    () => {
      opnFetch("/open/workspaces/" + workspaceId, {method: "DELETE"}).then(
        () => {
          useAlert().success("Workspace successfully removed.")
          workspacesStore.remove(workspaceId)
        },
      )
    },
  )
}

const leaveWorkSpace = (workspaceId) => {
  useAlert().confirm(
    "Do you really want to leave this workspace? You will lose access to all forms in this workspace.",
    () => {
      leaveWorkspaceLoadingState.value = true
      opnFetch("/open/workspaces/" + workspaceId + "/leave", {
        method: "POST",
      }).then(() => {
        useAlert().success("You have left the workspace.")
        workspacesStore.remove(workspaceId)
        getWorkspaceUsers()
      }).catch((error) => {
        useAlert().error("There was an error leaving the workspace.")
      }).finally(() => {
        leaveWorkspaceLoadingState.value = false
      })
    },
  )
}

const resendInvite = (id) => {
  const inviteId = users.value[id].id
  useAlert().confirm(
    "Do you really want to resend invite email to this user?",
    () => {
      opnFetch("/open/workspaces/" + workspace.value.id + "/invites/" + inviteId + "/resend", {method: "POST"}).then(
        () => {
          useAlert().success("Invitation resent successfully.")
          getWorkspaceUsers()
        },
      ).catch(err => {
        useAlert().error(err.response._data?.message)
      })
    })
}

const cancelInvite = (id) => {
  const inviteId = users.value[id].id
  useAlert().confirm(
    "Do you really want to cancel this user's invitation to this workspace?",
    () => {
      opnFetch("/open/workspaces/" + workspace.value.id + "/invites/" + inviteId + "/cancel", {method: "DELETE"}).then(
        () => {
          useAlert().success("Invitation cancelled successfully.")
          getWorkspaceUsers()
        },
      ).catch(err => {
        useAlert().error(err.response._data?.message)
      })
    })
}

</script>
