<template>
  <div class="flex flex-wrap flex-col flex-grow">
    <div
      key="2"
      class="w-full flex flex-grow flex-col"
    >
      <create-form-base-modal
        :show="showInitialFormModal"
        @form-generated="formGenerated"
        @close="showInitialFormModal = false"
      />

      <form-editor
        v-if="form && !workspacesLoading"
        ref="editor"
        class="w-full flex flex-grow"
        :error="error"
        @on-save="formInitialHash = null"
      />
      <div
        v-else
        class="text-center mt-4 py-6"
      >
        <Loader class="h-6 w-6 text-blue-500 mx-auto" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch } from "vue"
import { initForm } from "~/composables/forms/initForm.js"
import FormEditor from "~/components/open/forms/components/FormEditor.vue"
import CreateFormBaseModal from "../../../components/pages/forms/create/CreateFormBaseModal.vue"
import { useTemplates } from "~/composables/query/useTemplates"
import { hash } from "~/lib/utils.js"
import { onBeforeRouteLeave } from "vue-router"

definePageMeta({
  middleware: "auth",
})

useOpnSeoMeta({
  title: "Create a new Form",
})

onBeforeRouteLeave((to, from, next) => {
  if (isDirty()) {
      if (window.confirm('Changes you made may not be saved. Are you sure want to leave?')) {
        window.onbeforeunload = null
        next()
      } else {
        next(false)
      }
    }
  next()
})

const route = useRoute()
const workingFormStore = useWorkingFormStore()
const formStore = useFormsStore()

let template = null
if (route.query.template) {
  const { data, suspense } = useTemplates().detail(route.query.template)
  await suspense()
  template = data.value
}

const { current: workspace, isLoading: workspacesLoading } = useCurrentWorkspace()
const { content: form } = storeToRefs(workingFormStore)

// State
const loading = ref(false)
const error = ref("")
const showInitialFormModal = ref(false)
const formInitialHash = ref(null)

watch(
  () => workspace,
  () => {
    if (workspace) {
      form.workspace_id = workspace.value.id
    }
  },
)

onMounted(() => {
  if (import.meta.client) {
    window.onbeforeunload = () => {
      if (isDirty()) {
        return false
      }
    }
  }

  if (!formStore.allLoaded) {
    formStore.loadAll(workspace.value.id)
  }

  form.value = initForm({ workspace_id: workspace.value?.id, no_branding: workspace.value?.is_pro }, true)
  formInitialHash.value = hash(JSON.stringify(form.value.data()))
  if (template && template.structure) {
    form.value = useForm({ ...form.value.data(), ...template.structure })
  } else {
    // No template loaded, ask how to start
    showInitialFormModal.value = true
  }
  // workspacesStore.loadIfEmpty()
})

// Methods
const formGenerated = (newForm) => {
  form.value = useForm({ ...form.value.data(), ...newForm })
}

const isDirty = () => {
  return (
    !loading.value &&
    formInitialHash.value &&
    formInitialHash.value !== hash(JSON.stringify(form.value.data()))
  )
}
</script>
