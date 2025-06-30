<template>
  <div class="space-y-8">
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
          <text-input
            :form="workspaceForm"
            name="name"
            label="Workspace Name"
            placeholder="My Workspace"
            :required="true"
          />
          <text-input
            :form="workspaceForm"
            name="emoji"
            label="Emoji (optional)"
            placeholder="🚀"
            help="Choose an emoji to represent your workspace"
          />
        </div>

        <div class="mt-4">
          <UButton
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
      <div class="space-y-2">
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
    </div>
  </div>
</template>

<script setup>
const workspacesStore = useWorkspacesStore()
const alert = useAlert()
const appStore = useAppStore()
const router = useRouter()

const workspace = computed(() => workspacesStore.getCurrent)

// Workspace form
const workspaceForm = useForm({
  name: '',
  emoji: ''
})

// Delete workspace loading state
const deleteLoading = ref(false)

// Update profile
const updateProfile = () => {
  workspaceForm.put(`/open/workspaces/${workspace.value.id}/`).then((data) => {
    workspacesStore.save(data.workspace)
    useAlert().success('Workspace information successfully updated!')
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
const deleteWorkspace = () => {
  deleteLoading.value = true
  opnFetch("/open/workspaces/" + workspace.value.id, {method: "DELETE"})
    .then((data) => {
      deleteLoading.value = false
      alert.success(data.message)
      workspacesStore.remove(workspace.value.id)
      appStore.closeWorkspaceSettingsModal()
      router.push({ name: "home" })
    })
    .catch((error) => {
      alert.error(error.data.message)
      deleteLoading.value = false
    })
}


// Initialize form with user data
onBeforeMount(() => {
  if (workspace.value) {
    workspaceForm.keys().forEach((key) => {
      workspaceForm[key] = workspace.value[key]
    })
  }
})

// Watch for user changes
watch(workspace, (newWorkspace) => {
  if (newWorkspace) {
    workspaceForm.keys().forEach((key) => {
      workspaceForm[key] = newWorkspace[key]
    })
  }
}, { immediate: true })
</script> 