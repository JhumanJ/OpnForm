<template>
  <div class="w-full flex flex-col flex-grow">
    <form-editor
      v-if="form && !error"
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
      <Loader class="h-6 w-6 text-blue-500 mx-auto" />
    </div>
  </div>
</template>

<script setup>

import FormEditor from "~/components/open/forms/components/FormEditor.vue"
import { hash } from "~/lib/utils.js"

// Composables
const route = useRoute()
const workingFormStore = useWorkingFormStore()
const { detail: formDetail } = useForms()

const slug = route.params.slug

// Get form by slug using TanStack Query
const { data: form, isLoading: formsLoading, error } = formDetail(slug)

const updatedForm = storeToRefs(workingFormStore).content
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
  if (!form.value) {
    return
  }

  updatedForm.value = useForm(form.value)
  if (!updatedForm.value) {
    return
  }
  formInitialHash.value = hash(JSON.stringify(updatedForm.value.data()))
}

// Update working form store when form changes
watch(
  () => form.value,
  (newForm) => {
    workingFormStore.reset()
    if (newForm) {
      workingFormStore.set(newForm)
      initUpdatedForm()
    }
  },
  { immediate: true }
)

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
})

useOpnSeoMeta({
  title: "Edit " + (form.value ? form.value.title : "Your Form"),
})
definePageMeta({
  middleware: "auth",
})
</script>
