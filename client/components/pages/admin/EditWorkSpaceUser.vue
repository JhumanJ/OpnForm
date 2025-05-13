<template>
  <modal
    :show="showEditUserModal"
    max-width="lg"
    @close="$emit('close')"
  >
    <template #title>
      Edit User Role
    </template>
    <div class="px-4">
      <form
        @submit.prevent="updateUserRole"
      >
        <div>
          <FlatSelectInput
            v-model="userNewRole"
            name="newUserRole"
            :label="'New Role for '+props.user.name"
            :options="[
              { name: 'User', value: 'user' },
              { name: 'Admin', value: 'admin' },
              { name: 'Read Only', value: 'readonly' },
            ]"
            option-key="value"
            display-key="name"
          />
        </div>

        <div class="w-full mt-6">
          <v-button
            :loading="updatingUserRoleState"
            class="w-full my-3"
          >
            Update
          </v-button>
        </div>
      </form>
    </div>
  </modal>
</template>

<script setup>
import {watch, ref} from "vue"

 
const props = defineProps(['user', 'showEditUserModal'])
const emit = defineEmits(['close', 'fetchUsers'])

const workspacesStore = useWorkspacesStore()
const userNewRole = ref("")

const updatingUserRoleState = ref(false)

watch(() => props.user, () => {
  userNewRole.value = props.user.pivot.role
})

const updateUserRole = () => {
  updatingUserRoleState.value = true
  opnFetch(
    "/open/workspaces/" +
    workspacesStore.currentId +
    "/users/" +
    props.user.id +
    "/update-role",
    {
      method: "PUT",
      body: {
        role: userNewRole.value,
      },
    },
    {showSuccess: false},
  ).then(() => {
    useAlert().success("User role updated.")
    emit('fetchUsers')
    emit('close')
  }).catch(() => {
    useAlert().error("There was an error updating user role")
  }).finally(() => {
    updatingUserRoleState.value = false
  })
}

</script>
