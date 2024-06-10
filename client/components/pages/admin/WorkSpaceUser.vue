<template>
    <div>
        <h4 class="font-bold">Members</h4>
        <form v-if="isWorkspaceAdmin" @submit.prevent="addUser" class="my-2">
          <text-input
            name="email"
            v-model="newUser"
            label="Email"
            :required="true"
            placeholder="Add a new user by email"
          />
          <select-input
            name="newUserRole"
            v-model="newUserRole"
            :options="roleOptions"
            placeholder="Select User Role"
            label="Role"
            :required="true"
          />
          <div class="flex justify-end mt-2">
            <v-button
            color="outline-blue"
            :loading="loadingUsers"
          >
            Add User
          </v-button>
          </div>
        </form>

        <UTable
          :loading="loadingUsers"
          :rows="rows"
          :columns="columns"
        >
        <template #actions-data="{ row, index }" v-if="isWorkspaceAdmin">
          <UButton
            @click="editUser(index)"
            icon="i-heroicons-pencil"
            size="2xs"
            color="blue"
            variant="outline"
            :ui="{ rounded: 'rounded-full' }"
            square
          />

          <UButton
            @click="removeUser(index)"
            icon="i-heroicons-trash"
            size="2xs"
            color="red"
            variant="outline"
            :ui="{ rounded: 'rounded-full' }"
            square
          />
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
const roleOptions = [
  { name: "User", value: "user" },
  { name: "Admin", value: "admin" }
]
const newUser = ref("")
const newUserRole = ref("user")
const showEditUserModal = ref(false)
const selectedUser = ref(null)
const userNewRole = ref("")
const updatingUserRoleState = ref(false)

onMounted(() => {
  getWorkspaceUsers()
})

const getWorkspaceUsers = async () => {
  loadingUsers.value = true
  let data = await workspacesStore.getWorkspaceUsers()
  users.value = data.data.value
  loadingUsers.value = false
}

const isWorkspaceAdmin = computed(() => {
  let user = users.value.find((user) => user.id === authStore.user.id)
  return user && user.pivot.role === "admin"
})

const rows = computed(() => {
  return users.value.filter((user) => user.id !== authStore.user.id)
})

const columns = computed(()=>{
  return [
      { key: 'name', label: 'Name' },
      { key: 'email', label: 'Email' },
      { key: 'pivot.role', label: 'Role' },
      ...(isWorkspaceAdmin.value ? [{ key: 'actions', label: 'Action' }] : [])
    ]
})

const addUser = () => {
  if (!newUser.value) return
  loadingUsers.value = true
  opnFetch(
    "/open/workspaces/" + workspacesStore.currentId + "/users/add",
    {
      method: "POST",
      body: {
        email: newUser.value,
        role: newUserRole.value,
      },
    },
    { showSuccess: false },
  ).then((data) => {
    newUser.value = ""
    newUserRole.value = "user"

    useAlert().success(data.message)

    getWorkspaceUsers()
  }).catch((error) => {
    useAlert().error("There was an error adding user")
  }).finally(() => {
    loadingUsers.value = false
  })
}

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

</script>