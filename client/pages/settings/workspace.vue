<template>
  <div>
    <div class="flex flex-wrap items-center gap-y-4">
      <div class="flex-grow">
        <div class="flex items-center gap-2">
          <h3 class="font-semibold text-2xl text-gray-900">
            Workspace settings
          </h3>
          <UTooltip text="Edit workspace">
            <UButton
              v-if="!workspace.is_readonly"
              size="xs"
              color="white"
              icon="i-heroicons-pencil-square"
              @click="editCurrentWorkspace"
            />
          </UTooltip>
        </div>
        <small class="text-gray-500">You're currently editing the settings for the workspace "{{ workspace.name }}".
          You can switch to another workspace in top left corner of the page.</small>
      </div>
      <div class="w-full flex flex-wrap gap-2">
        <template v-if="!workspace.is_readonly">
          <WorkSpaceCustomDomains v-if="useFeatureFlag('custom_domains') && !loading" />
          <WorkSpaceEmailSettings v-if="!loading" />
        </template>
        <UButton
          label="New Workspace"
          icon="i-heroicons-plus"
          :loading="loading"
          @click="workspaceModal = true"
        />
      </div>
    </div>

    <div
      v-if="loading"
      class="w-full text-blue-500 text-center"
    >
      <Loader class="h-10 w-10 p-5" />
    </div>
    <div
      v-else-if="workspace"
      class="my-4"
    >
      <WorkSpaceUser />
    </div>

    <!--  Workspace modal  -->
    <modal
      :show="workspaceModal"
      max-width="lg"
      :compact-header="true"
      @close="workspaceModal = false"
    >
      <template #icon>
        <Icon
          :name="isEditing ? 'heroicons:pencil-square' : 'heroicons:plus-circle'"
          class="w-8 h-8"
        />
      </template>
      <template #title>
        {{ isEditing ? 'Edit' : 'Create' }} Workspace
      </template>
      <div class="px-4">
        <form
          @submit.prevent="isEditing ? updateWorkspace() : createWorkspace()"
          @keydown="form.onKeydown($event)"
        >
          <div>
            <text-input
              name="name"
              class="mt-4"
              :form="form"
              :required="true"
              label="Workspace Name"
            />
            <text-input
              name="emoji"
              class="mt-4"
              :form="form"
              :required="false"
              label="Emoji"
            />
          </div>

          <div class="w-full mt-6">
            <v-button
              :loading="form.busy"
              class="w-full my-3"
            >
              Save
            </v-button>
          </div>
        </form>
      </div>
    </modal>
  </div>
</template>

<script setup>
import {fetchAllWorkspaces} from "~/stores/workspaces.js"

const workspacesStore = useWorkspacesStore()
const workspace = computed(() => workspacesStore.getCurrent)
const loading = computed(() => workspacesStore.loading)

useOpnSeoMeta({
  title: "Workspaces",
})
definePageMeta({
  middleware: "auth",
})

const form = useForm({
  name: "",
  emoji: "",
})
const workspaceModal = ref(false)
const isEditing = ref(false)

onMounted(() => {
  fetchAllWorkspaces()
})

const editCurrentWorkspace = () => {
  isEditing.value = true
  form.name = workspace.value.name
  form.emoji = workspace.value.icon || ''
  workspaceModal.value = true
}

const updateWorkspace = () => {
  form.put(`/open/workspaces/${workspace.value.id}/`).then((data) => {
    workspacesStore.save(data.workspace)
    workspaceModal.value = false
    isEditing.value = false
    useAlert().success('Workspace successfully updated!')
  })
}

const createWorkspace = () => {
  form.post("/open/workspaces/create").then((data) => {
    workspacesStore.save(data.workspace)
    workspacesStore.currentId = data.workspace.id
    workspaceModal.value = false
    useAlert().success(
      "Workspace successfully created! You are now editing settings for your new workspace.",
    )
  })
}

watch(workspaceModal, (newValue) => {
  if (!newValue) {
    isEditing.value = false
    form.reset()
  }
})
</script>
