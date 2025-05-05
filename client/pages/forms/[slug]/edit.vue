<template>
  <div class="w-full flex flex-col flex-grow">
    <form-editor
      v-if="(!formsLoading || form ) && !error "
      ref="editor"
      :is-edit="true"
      @on-save="formInitialHash = null"
    />
    <div
      v-else-if="error && !formsLoading"
      class="mt-4 rounded-lg max-w-xl mx-auto p-6 bg-red-100 text-red-500"
    >
      {{ error }}
    </div>
    <div
      v-else
      class="text-center mt-4 py-6"
    >
      <Loader class="h-6 w-6 text-nt-blue mx-auto" />
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
import FormEditor from "~/components/open/forms/components/FormEditor.vue"
import { hash } from "~/lib/utils.js"

const formsStore = useFormsStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()

if (!formsStore.allLoaded) {
  formsStore.startLoading()
}
const updatedForm = storeToRefs(workingFormStore).content
const form = computed(() => formsStore.getByKey(useRoute().params.slug))
const formsLoading = computed(() => formsStore.loading)

const error = ref(null)
const formInitialHash = ref(null)

function isDirty() {
  try {
    return (
      formInitialHash.value &&
      updatedForm.value &&
      formInitialHash.value !==
        hash(JSON.stringify(updatedForm?.value?.data() ?? null))
    )
  } catch {
    return false
  }
}

function initUpdatedForm() {
  if (!form.value || !form.value) {
    return
  }

  updatedForm.value = useForm(form.value)
  if (!updatedForm.value) {
    return
  }
  formInitialHash.value = hash(JSON.stringify(updatedForm.value.data()))
}

// Create a form.id watcher that updates working form
watch(form, (form) => {
  if (form?.value) {
    initUpdatedForm()
    
  }
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

onBeforeMount(() => {
  if (import.meta.client) {
    window.onbeforeunload = () => {
      if (isDirty()) {
        return false
      }
    }
  }

  if (!form.value && !formsStore.allLoaded) {
    formsStore.loadAll(workspacesStore.currentId).then(() => {
      initUpdatedForm()
    })
  } else {
    initUpdatedForm()
  }
})

useOpnSeoMeta({
  title: "Edit " + (form.value && form.value ? form.value.title : "Your Form"),
})
definePageMeta({
  middleware: "auth",
})
</script>
