<template>
  <div class="flex flex-wrap flex-col flex-grow">
    <create-form-base-modal
      :show="showInitialFormModal"
      @form-generated="formGenerated"
      @close="showInitialFormModal = false"
    />
    <form-editor
      v-if="!workspacesLoading"
      ref="editor"
      class="w-full flex flex-grow"
      :error="error"
      :is-guest="isGuest"
      @open-register="appStore.quickRegisterModal = true"
    />
    <div
      v-else
      class="text-center mt-4 py-6"
    >
      <Loader class="h-6 w-6 text-blue-500 mx-auto" />
    </div>
  </div>
</template>

<script setup>
import FormEditor from "~/components/open/forms/components/FormEditor.vue"
import CreateFormBaseModal from "../../../components/pages/forms/create/CreateFormBaseModal.vue"
import { initForm } from "~/composables/forms/initForm.js"
import { useTemplates } from "~/composables/query/useTemplates"

import { WindowMessageTypes } from "~/composables/useWindowMessage"

const appStore = useAppStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()
const route = useRoute()

let template = null
if (route.query.template) {
  const { data, suspense } = useTemplates().detail(route.query.template)
  await suspense()
  template = data.value
}

// Store values
const workspacesLoading = computed(() => workspacesStore.loading)
const form = storeToRefs(workingFormStore).content

useOpnSeoMeta({
  title: "Create a new Form for free",
})
definePageMeta({
  middleware: ["guest", "self-hosted"],
})

// Data
const stateReady = ref(false)
const error = ref("")
const isGuest = ref(true)
const showInitialFormModal = ref(false)

// Component ref
const editor = ref(null)

onMounted(() => {
  // Set as guest user
  workspacesStore.set([
    {
      id: null,
      name: "Guest Workspace",
      is_enterprise: false,
      is_pro: false,
    },
  ])

  form.value = initForm({}, true)
  if (template && template.structure) {
    form.value = useForm({ ...form.value.data(), ...template.structure })
  } else {
    // No template loaded, ask how to start
    showInitialFormModal.value = true
  }
  stateReady.value = true

  // Set up window message listener for after-login
  const afterLoginMessage = useWindowMessage(WindowMessageTypes.AFTER_LOGIN)
  afterLoginMessage.listen(() => {
    afterLogin()
  }, { useMessageChannel: false })
})

const { invalidateAll } = useWorkspaces()

const afterLogin = () => {
  isGuest.value = false
  invalidateAll() // Refetch all workspace queries
  setTimeout(() => {
    if (editor) {
      editor.value.saveFormCreate()
    }
  }, 500)
}

const formGenerated = (newForm) => {
  form.value = useForm({ ...form.value.data(), ...newForm })
}
</script>
