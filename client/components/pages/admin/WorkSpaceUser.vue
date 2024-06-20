<template>
    <div class="mt-4">
      <div class="flex items-center justify-between">

        <h4 class="font-bold">Members</h4>
        <v-button
          color="outline-blue"
          :loading="loading"
          @click="userInviteModal = true"
        >
          <Icon name="heroicons:plus-16-solid" class="w-5 h-5"/>
          Invite User
        </v-button>
      </div>
        <!--  User invite modal  -->
        <modal
          :show="userInviteModal"
          max-width="lg"
          @close="userInviteModal = false"
        >
          <AddUserToWorkspace :isWorkspaceAdmin="isWorkspaceAdmin" @fetchUsers="getWorkspaceUsers" />
        </modal>
        <UTable
          :loading="loadingUsers"
          :rows="rows"
          :columns="columns"
        >
        <template #actions-data="{ row, index }" v-if="isWorkspaceAdmin" class="">
          <div class="space-x-2">

            <UTooltip text="Edit user" v-if="row.type == 'user'">
              <UButton
                @click="editUser(index)"
                icon="i-heroicons-pencil"
                size="2xs"
                color="blue"
                variant="outline"
                :ui="{ rounded: 'rounded-full' }"
                square
              />
            </UTooltip>
            <UTooltip text="Remove user" v-if="row.type == 'user'">
              <UButton
                v-if="row.type == 'user'"
                @click="removeUser(index)"
                icon="i-heroicons-trash"
                size="2xs"
                color="red"
                variant="outline"
                :ui="{ rounded: 'rounded-full' }"
                square
              />
            </UTooltip>
            <UTooltip text="Resend Invite" v-if="row.type == 'invitee'">
              <UButton
                @click="resendInvite(index)"
                icon="i-heroicons-envelope"
                size="2xs"
                color="green"
                variant="outline"
                :ui="{ rounded: 'rounded-full' }"
                square
              />
            </UTooltip>
            <UTooltip text="Cancel Invite" v-if="row.type == 'invitee'">
              <UButton
                @click="cancelInvite(index)"
                icon="i-heroicons-x-mark"
                size="2xs"
                color="red"
                variant="outline"
                :ui="{ rounded: 'rounded-full' }"
                square
              />
            </UTooltip>
          </div>
        </template>
        </UTable>
        
        <EditWorkSpaceUser 
          :user="selectedUser" 
          :show-edit-user-modal="showEditUserModal" 
          @close="showEditUserModal = false" 
          @fetch-users="getWorkspaceUsers"
        />

    <div class="flex flex-wrap justify-between gap-2 mt-4 mb-3">
        <v-button
          v-if="users.length > 1"
          color="white"
          class="group w-full sm:w-auto"
          :loading="leaveWorkspaceLoadingState"
          @click="leaveWorkSpace(workspace.id)"
        >
          Leave Workspace
        </v-button>
        
        <v-button
          v-if="isWorkspaceAdmin && users.length == 1"
          color="white"
          class="group w-full sm:w-auto"
          :loading="loading"
          @click="deleteWorkspace(workspace.id)"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 -mt-1 inline group-hover:text-red-700"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
            />
          </svg>
          Remove workspace
        </v-button>
      </div>
    </div>
</template>

<script setup>
import { watch, ref } from "vue"

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

onMounted(() => {
  getWorkspaceUsers()
})

const getWorkspaceUsers = async () => {
  userInviteModal.value = false;
  loadingUsers.value = true
  let data = await workspacesStore.getWorkspaceUsers()
  data = data.map(d => {
    return { 
      ...d,
      name: d.name,
      email:d.email,
      status: 'accepted',
      role:d.pivot.role,
      type:'user'
     }
  })
  let invites = await workspacesStore.getWorkspaceInvites()
  invites = invites.filter(i=>i.status!== 'accepted').map(i=>{
    return {
      ...i,
      name: 'Invitee',
      email:i.email,
      status:i.status,
      type:'invitee'
    }
  })
  users.value = [...data, ...invites]
  loadingUsers.value = false
}

const isWorkspaceAdmin = computed(() => {
  if(!users.value) return false
  let user = users.value.find((user) => user.id === authStore.user.id)
  return user && user.pivot.role === "admin"
})

const rows = computed(() => {
  if(users.value){
    return users.value.filter((user) => user.id !== authStore.user.id)
  }
})

const columns = computed(()=>{
  return [
      { key: 'name', label: 'Name' },
      { key: 'email', label: 'Email' },
      { key: 'role', label: 'Role' },
      ...(isWorkspaceAdmin.value ? [{ key: 'actions', label: 'Action' }] : [])
    ]
})



const editUser = (row) => {
  selectedUser.value = rows.value[row]
  userNewRole.value = selectedUser.value.pivot.role
  showEditUserModal.value = true
}



const removeUser = (index) => {
  let user = rows.value[index]
  useAlert().confirm(
    "Do you really want to remove " + user.name + " from this workspace?",
    () => {
      loadingUsers.value = true
      opnFetch(
        "/open/workspaces/" + workspacesStore.currentId + "/users/" + user.id + "/remove",
        {
          method: "DELETE",
        },
        { showSuccess: false },
      ).then(() => {
        useAlert().success("User successfully Removed removed.")
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
      opnFetch("/open/workspaces/" + workspaceId, { method: "DELETE" }).then(
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
  const inviteId = rows.value[id].id
  useAlert().confirm(
    "Do you really want to resend invite email to this user?",
    () => {
      opnFetch("/open/workspaces/" + workspace.value.id + "/invites/" + inviteId + "/resend", { method: "POST" }).then(
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
  const inviteId = rows.value[id].id
  useAlert().confirm(
    "Do you really want to cancel this user's invitation to this workspace?",
    () => {
      opnFetch("/open/workspaces/" + workspace.value.id + "/invites/" + inviteId + "/cancel", { method: "DELETE" }).then(
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