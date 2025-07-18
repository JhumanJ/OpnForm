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
        :loading="addMutation.isPending.value"
        icon="i-heroicons-envelope"
      >
        Invite User
      </UButton>
    </div>
  </form>
</template>

<script setup>
defineProps({
  isWorkspaceAdmin: { type: Boolean, default: false },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const { currentId } = useCurrentWorkspace()
const { addUser: addUserMutation } = useWorkspaceUsers()

const roleOptions = [
  {name: "User", value: "user"},
  {name: "Admin", value: "admin"},
  {name: "Read Only", value: "readonly"}
]

const newUser = ref("")
const newUserRole = ref("user")

const addMutation = addUserMutation(currentId)

const addUser = () => {
  if (!newUser.value) return
  
  addMutation.mutateAsync({
    email: newUser.value,
    role: newUserRole.value,
  }).then((data) => {
    newUser.value = ""
    newUserRole.value = "user"
    useAlert().success(data.message)
    // No need to emit 'fetchUsers' - the mutation handles cache updates automatically
  }).catch((error) => {
    useAlert().error("There was an error adding user: " + error.data.message)
  })
}
</script>
