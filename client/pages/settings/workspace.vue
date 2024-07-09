<template>
  <div>
    <div class="flex flex-wrap items-center gap-y-4">
      <div class="flex-grow">
        <h3 class="font-semibold text-2xl text-gray-900">
          Workspace settings
        </h3>
        <small class="text-gray-500">You're currently editing the settings for the workspace "{{ workspace.name }}".
          You can switch to another workspace in top left corner of the page.</small>
      </div>
      <div class="w-full flex flex-wrap justify-between gap-2">
        <WorkSpaceCustomDomains v-if="customDomainsEnabled && !loading" />
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
      @close="workspaceModal = false"
    >
      <template #icon>
        <svg
          class="w-8 h-8"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M12 8V16M8 12H16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </template>
      <template #title>
        Create Workspace
      </template>
      <div class="px-4">
        <form
          @submit.prevent="createWorkspace"
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
import {watch, ref} from "vue"
import {fetchAllWorkspaces} from "~/stores/workspaces.js"

const crisp = useCrisp()
const workspacesStore = useWorkspacesStore()
const workspaces = computed(() => workspacesStore.getAll)
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

const workspace = computed(() => workspacesStore.getCurrent)
const customDomainsEnabled = computed(
  () => useRuntimeConfig().public.customDomainsEnabled,
)

onMounted(() => {
  fetchAllWorkspaces()
})

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
</script>
