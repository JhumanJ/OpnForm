<template>
  <UModal
    v-model:open="showEditUserModal"
    :ui="{ width: 'lg:max-w-lg' }"
    @close="$emit('close')"
    title="Edit User Role"
  >
    <template #body>
      <UCard>
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
              <UButton
                :loading="updateMutation.isPending.value"
                class="my-3"
                block
                label="Update"
              />
            </div>
          </form>
        </div>
      </UCard>
    </template>
  </UModal>
</template>

<script setup>
import {watch, ref} from "vue"
import { useWorkspaceUsers } from "~/composables/query/useWorkspaceUsers"
import { useCurrentWorkspace } from "~/composables/query/useCurrentWorkspace"

 
const props = defineProps(['user', 'showEditUserModal'])
const emit = defineEmits(['close', 'fetchUsers'])

const { currentId } = useCurrentWorkspace()
const { updateUserRole: updateUserRoleMutation } = useWorkspaceUsers()

const userNewRole = ref("")

const updateMutation = updateUserRoleMutation(currentId, {
  onSuccess: () => {
    useAlert().success("User role updated.")
    emit('close')
    // No need to emit 'fetchUsers' - the mutation handles cache updates automatically
  },
  onError: () => {
    useAlert().error("There was an error updating user role")
  }
})

watch(() => props.user, () => {
  userNewRole.value = props.user.pivot.role
})

const updateUserRole = () => {
  updateMutation.mutate({
    userId: props.user.id,
    data: { role: userNewRole.value }
  })
}
</script>
