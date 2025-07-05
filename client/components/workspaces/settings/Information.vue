<template>
  <div class="space-y-4">
    <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Workspace Information</h3>
        <p class="mt-1 text-sm text-neutral-500">
          Update your workspace information.
        </p>
      </div>
    </div>

    <VForm size="sm">
      <form
        @submit.prevent="updateProfile"
        @keydown="workspaceForm.onKeydown($event)"
      >
        <div class="max-w-sm">
          <TextInput
            :disabled="workspace.is_readonly"
            :form="workspaceForm"
            name="name"
            label="Workspace Name"
            placeholder="My Workspace"
            :required="true"
          />
          <TextInput
            :disabled="workspace.is_readonly"
            :form="workspaceForm"
            name="emoji"
            label="Emoji (optional)"
            placeholder="Emoji"
            help="Choose an emoji to represent your workspace"
          />
        </div>

        <div class="mt-4">
          <UButton
            :disabled="workspace.is_readonly"
            type="submit"
            :loading="workspaceForm.busy"
            color="primary"
          >
            Save Changes
          </UButton>
        </div>
      </form>
    </VForm>

    <div class="pt-8 border-t border-neutral-200">
      <div 
        v-if="user.admin" 
        class="space-y-2"
      >
        <h4 class="text-red-800 font-medium">Delete Workspace</h4>
        <p class="text-neutral-500 text-sm">
          This will permanently delete your entire workspace. All forms created in this workspace will be removed. This cannot be undone.
        </p>
        <UButton
          color="error"
          :loading="deleteLoading"
          @click="confirmDeleteWorkspace"
        >
          Delete workspace
        </UButton>
      </div>

      <div 
        v-else
        class="space-y-2"
      >
        <h4 class="text-neutral-900 font-medium">Leave Workspace</h4>
        <p class="text-neutral-500 text-sm">
          This will remove you from the workspace. You will lose access to all forms in this workspace.
        </p>
        <UButton
          color="neutral"
          :loading="leaveWorkspaceLoading"
          @click="leaveWorkSpace"
        >
          Leave workspace
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup>
const { current, update, remove, leave } = useWorkspaces()
const authStore = useAuthStore()
const alert = useAlert()
const appStore = useAppStore()
const router = useRouter()

const workspace = computed(() => current().data)
const user = computed(() => authStore.user)

// Workspace form
const workspaceForm = useForm({
  name: '',
  emoji: ''
})

const deleteLoading = ref(false)
const leaveWorkspaceLoading = ref(false)

// Update profile
const updateMutation = update()
const updateProfile = () => {
  updateMutation.mutate({
    id: workspace.value.id,
    data: workspaceForm.data()
  }, {
    onSuccess: () => {
    useAlert().success('Workspace information successfully updated!')
    },
    onError: (error) => {
      console.error('Error updating workspace:', error)
    }
  })
}

// Delete workspace confirmation
const confirmDeleteWorkspace = () => {
  alert.confirm(
    'Do you really want to delete your workspace?',
    deleteWorkspace
  )
}

// Delete workspace
const removeMutation = remove()
const deleteWorkspace = () => {
  deleteLoading.value = true
  removeMutation.mutate(workspace.value.id, {
    onSuccess: (data) => {
      deleteLoading.value = false
      alert.success(data.message)
      appStore.closeWorkspaceSettingsModal()
      router.push({ name: "home" })
    },
    onError: (error) => {
      alert.error(error.data?.message || 'Error deleting workspace')
      deleteLoading.value = false
    }
    })
}

// Leave workspace
const leaveMutation = leave()
const leaveWorkSpace = () => {
  alert.confirm(
    "Do you really want to leave this workspace? You will lose access to all forms in this workspace.",
    () => {
      leaveWorkspaceLoading.value = true
      leaveMutation.mutate(workspace.value.id, {
        onSuccess: () => {
        alert.success("You have left the workspace.")
        appStore.closeWorkspaceSettingsModal()
        router.push({ name: "home" })
        },
        onError: () => {
        alert.error("There was an error leaving the workspace.")
        leaveWorkspaceLoading.value = false
        }
      })
    },
  )
}


// Watch for user changes
watch(workspace, (newWorkspace) => {
  if (newWorkspace) {
    workspaceForm.fill({
      name: newWorkspace.name || '',
      emoji: newWorkspace.icon || ''
    })
  }
}, { immediate: true })
</script> 