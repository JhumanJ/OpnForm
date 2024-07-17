<template>
  <form
    v-if="isWorkspaceAdmin"
    class="my-2"
    @submit.prevent="addUser"
  >
    <text-input
      v-model="newUser"
      name="email"
      label="Email"
      :required="true"
      :disabled="disabled"
      placeholder="Add a new user by email"
    />
    <select-input
      v-model="newUserRole"
      name="newUserRole"
      :options="roleOptions"
      :disabled="disabled"
      placeholder="Select User Role"
      label="Role"
      :required="true"
    />
    <div class="flex justify-center mt-2">
      <UButton
        type="submit"
        :disabled="disabled"
        :loading="addingUsersState"
        icon="i-heroicons-envelope"
      >
        Invite User
      </UButton>
    </div>
  </form>
</template>

<script setup>
const props = defineProps({
  isWorkspaceAdmin: {},
  disabled: {
    type: Boolean,
    default: false,
  },
})
const emit = defineEmits(['fetchUsers'])

const workspacesStore = useWorkspacesStore()

const roleOptions = [
  {name: "User", value: "user"},
  {name: "Admin", value: "admin"}
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
    useAlert().error("There was an error adding user: " + error.data.message)
  }).finally(() => {
    addingUsersState.value = false
  })
}

</script>
