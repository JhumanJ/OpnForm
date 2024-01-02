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

definePageMeta({
  middleware: "auth"
})

// metaTitle: 'Create a new Form',

// beforeRouteLeave (to, from, next) {
//   if (this.isDirty()) {
//     return useAlert().confirm('Changes you made may not be saved. Are you sure want to leave?', () => {
//       window.onbeforeunload = null
//       next()
//     }, () => {})
//   }
//   next()
// },

const route = useRoute()
const authStore = useAuthStore()
const templatesStore = useTemplatesStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()
const formStore = useFormsStore()

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
  if (process.client) {
    // window.onbeforeunload = () => {
    if (isDirty()) {
      return false
    }
  }

  if (!formStore.allLoaded) {
    formStore.load(workspace.value.id)
  }

  form.value = initForm({workspace_id: workspace.value?.id})
  formInitialHash.value = hashString(JSON.stringify(form.value.data()))
  // if (this.$route.query.template !== undefined && this.$route.query.template) {
  //   const template = this.templatesStore.getByKey(this.$route.query.template)
  //   if (template && template.structure) {
  //     this.form = new Form({...this.form.data(), ...template.structure})
  //   }
  // } else {
  //   // No template loaded, ask how to start
  //   this.showInitialFormModal = true
  // }
  // this.closeAlert()
  // this.workspacesStore.loadIfEmpty()
})

// Methods
const formGenerated = (newForm) => {
  form.value = useForm({...form.value.data(), ...newForm})
}

const isDirty = () => {
  return !loading.value && formInitialHash.value && formInitialHash.value !== hashString(JSON.stringify(form.value.data()))
}

const hashString = (str, seed = 0) => {
  let h1 = 0xdeadbeef ^ seed
  let h2 = 0x41c6ce57 ^ seed
  for (let i = 0, ch; i < str.length; i++) {
    ch = str.charCodeAt(i)
    h1 = Math.imul(h1 ^ ch, 2654435761)
    h2 = Math.imul(h2 ^ ch, 1597334677)
  }

  h1 = Math.imul(h1 ^ (h1 >>> 16), 2246822507) ^ Math.imul(h2 ^ (h2 >>> 13), 3266489909)
  h2 = Math.imul(h2 ^ (h2 >>> 16), 2246822507) ^ Math.imul(h1 ^ (h1 >>> 13), 3266489909)

  return 4294967296 * (2097151 & h2) + (h1 >>> 0)
}
</script>
