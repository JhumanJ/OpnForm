<template>
  <div class="flex flex-wrap flex-col">
    <transition name="fade" mode="out-in">
      <div key="2">
        <create-form-base-modal :show="showInitialFormModal" @form-generated="formGenerated"
                                @close="showInitialFormModal=false"
        />

        <form-editor v-if="form && !workspacesLoading" ref="editor"
                     class="w-full flex flex-grow"
                     :error="error"
                     @on-save="formInitialHash=null"
        />
        <div v-else class="text-center mt-4 py-6">
          <Loader class="h-6 w-6 text-nt-blue mx-auto"/>
        </div>
      </div>
    </transition>
  </div>
</template>


<script setup>
import {watch} from 'vue'
import {initForm} from "~/composables/forms/initForm.js"
import FormEditor from "~/components/open/forms/components/FormEditor.vue"
import CreateFormBaseModal from '../../../components/pages/forms/create/CreateFormBaseModal.vue'
import {fetchTemplate} from "~/stores/templates.js"
import {hash} from "~/lib/utils.js"
import {onBeforeRouteLeave} from 'vue-router'

definePageMeta({
  middleware: "auth"
})

useOpnSeoMeta({
  title: 'Create a new Form'
})

onBeforeRouteLeave((to, from, next) => {
  if (isDirty()) {
    return useAlert().confirm('Changes you made may not be saved. Are you sure want to leave?', () => {
      window.onbeforeunload = null
      next()
    }, () => {})
  }
  next()
})

const route = useRoute()
const authStore = useAuthStore()
const templatesStore = useTemplatesStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()
const formStore = useFormsStore()

// Fetch the template
if (route.query.template !== undefined && route.query.template) {
  const {data} = await fetchTemplate(route.query.template)
  templatesStore.save(data.value)
}

const {
  getCurrent: workspace,
  getAll: workspaces,
  workspacesLoading: workspacesLoading
} = storeToRefs(workspacesStore)
const {content: form} = storeToRefs(workingFormStore)

// State
const loading = ref(false)
const error = ref('')
const showInitialFormModal = ref(false)
const formInitialHash = ref(null)

watch(() => workspace, () => {
  if (workspace) {
    form.workspace_id = workspace.value.id
  }
})

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

  form.value = initForm({workspace_id: workspace.value?.id}, true)
  formInitialHash.value = hash(JSON.stringify(form.value.data()))
  if (route.query.template !== undefined && route.query.template) {
    const template = templatesStore.getByKey(route.query.template)
    if (template && template.structure) {
      form.value = useForm({...form.value.data(), ...template.structure})
    }
  } else {
    // No template loaded, ask how to start
    showInitialFormModal.value = true
  }
  // workspacesStore.loadIfEmpty()
})

// Methods
const formGenerated = (newForm) => {
  form.value = useForm({...form.value.data(), ...newForm})
}

const isDirty = () => {
  return !loading.value && formInitialHash.value && formInitialHash.value !== hash(JSON.stringify(form.value.data()))
}
</script>
