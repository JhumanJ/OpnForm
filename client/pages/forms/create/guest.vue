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
      <Loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
  </div>
</template>

<script setup>
import FormEditor from "~/components/open/forms/components/FormEditor.vue"
import CreateFormBaseModal from "../../../components/pages/forms/create/CreateFormBaseModal.vue"
import { initForm } from "~/composables/forms/initForm.js"
import { fetchTemplate } from "~/stores/templates.js"
import { fetchAllWorkspaces } from "~/stores/workspaces.js"
import { WindowMessageTypes } from "~/composables/useWindowMessage"

const appStore = useAppStore()
const templatesStore = useTemplatesStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()
const route = useRoute()

// Fetch the template
if (route.query.template !== undefined && route.query.template) {
  const { data } = await fetchTemplate(route.query.template)
  templatesStore.save(data.value)
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
  if (route.query.template !== undefined && route.query.template) {
    const template = templatesStore.getByKey(route.query.template)
    if (template && template.structure) {
      form.value = useForm({ ...form.value.data(), ...template.structure })
    }
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

const afterLogin = () => {
  isGuest.value = false
  fetchAllWorkspaces()
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
