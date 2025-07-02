<template>
  <UModal
    v-model:open="isOpen"
    :ui="{ content: 'sm:max-w-lg' }"
    title="Change form's workspace"
  >
    <template #body>
      <div class="flex space-x-4 items-center">
        <p>Current workspace:</p>
        <div class="flex items-center cursor group p-2 rounded-sm border">
          <WorkspaceIcon :workspace="workspace" />
          <p
            class="lg:block max-w-10 truncate ml-2 text-gray-800 dark:text-gray-200"
          >
            {{ workspace.name }}
          </p>
        </div>
      </div>
      <form @submit.prevent="onSubmit">
        <div class="my-4">
          <select-input
            v-model="selectedWorkspace"
            name="workspace"
            class=""
            :options="workspacesSelectOptions"
            :required="true"
            label="Select destination workspace"
          />
        </div>
      </form>
    </template>

    <template #footer>
      <div class="flex justify-end gap-x-2">
        <UButton
          color="neutral"
          variant="outline"
          @click="close"
          label="Close"
        />
        <UButton
          :loading="loading"
          @click="onSubmit"
          label="Change workspace"
        />
      </div>
    </template>
  </UModal>
</template>

<script setup>
import { ref, defineProps, defineEmits, computed } from "vue"
import WorkspaceIcon from "~/components/workspaces/WorkspaceIcon.vue"

const emit = defineEmits(["close"])
const workspacesStore = useWorkspacesStore()
const formsStore = useFormsStore()

const selectedWorkspace = ref(null)
const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
})

// Modal state
const isOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      close()
    }
  }
})

const workspaces = computed(() => workspacesStore.getAll)
const workspace = computed(() =>
  workspacesStore.getByKey(props.form?.workspace_id),
)
const loading = ref(false)
const workspacesSelectOptions = computed(() =>
  workspaces.value
    .filter((w) => {
      return w.id !== workspace.value.id
    })
    .map((workspace) => ({ name: workspace.name, value: workspace.id })),
)

const close = () => {
  emit("close")
}

const onSubmit = () => {
  const endpoint =
    "/open/forms/" + props.form.id + "/workspace/" + selectedWorkspace.value
  if (!selectedWorkspace.value) {
    useAlert().error("Please select a workspace!")
    return
  }
  opnFetch(endpoint, { method: "POST" })
    .then(() => {
      loading.value = false
      emit("close")
      useAlert().success("Form workspace updated successfully.")
      workspacesStore.setCurrentId(selectedWorkspace.value)
      formsStore.resetState()
      formsStore.loadAll(selectedWorkspace.value)
      const router = useRouter()
      const route = useRoute()
      if (route.name !== "home") {
        router.push({ name: "home" })
      }
      formsStore.loadAll(selectedWorkspace.value)
    })
    .catch((error) => {
      useAlert().error(
        error?.data?.message ?? "Something went wrong, please try again!",
      )
      loading.value = false
    })
}
</script>
