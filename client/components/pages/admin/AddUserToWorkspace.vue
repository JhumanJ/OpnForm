<template>
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
            :loading="addingUsersState"
          >
            Add User
          </v-button>
          </div>
        </form>
</template>

<script setup>
import { watch, ref } from "vue"

defineProps(['isWorkspaceAdmin'])
const emit = defineEmits(['fetchUsers'])

const workspacesStore = useWorkspacesStore()

const roleOptions = [
  { name: "User", value: "user" },
  { name: "Admin", value: "admin" }
]

const newUser = ref("")
const newUserRole = ref("user")
const addingUsersState = ref(false)


const addUser = () => {
  if (!newUser.value) return
  addingUsersState.value = true
  opnFetch(
    "/open/workspaces/" + workspacesStore.currentId + "/users/add",
    {
      method: "POST",
      body: {
        email: newUser.value,
        role: newUserRole.value,
      },
    }
  ).then((data) => {
    newUser.value = ""
    newUserRole.value = "user"

    useAlert().success(data.message)

    emit("fetchUsers")
  }).catch((error) => {
    useAlert().error("There was an error adding user")
  }).finally(() => {
    addingUsersState.value = false
  })
}

</script>