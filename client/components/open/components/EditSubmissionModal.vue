<template>
  <UModal
    v-model:open="isModalOpen"
    :ui="{ content: 'sm:max-w-2xl' }"
  >
    <template #header>
      <div class="flex items-center justify-between w-full">
        <h2 class="font-semibold">
          Edit Submission
        </h2>
        <UButton
          v-if="props.form?.editable_submissions ?? false"
          variant="outline"
          :color="copySuccess ? 'success' : 'primary'"
          :icon="copySuccess ? 'i-heroicons-check' : 'i-heroicons-clipboard-document'"
          @click.prevent="copyToClipboard"
        >
          <span class="hidden md:inline">{{ copySuccess ? 'Copied!' : 'Copy Public Link' }}</span>
        </UButton>
      </div>
    </template>
    <template #body>
      <OpenForm
        v-if="form"
        :form-manager="formManager"
        :theme="theme"
        @submit="updateForm"
      >
        <template #submit-btn="{ isProcessing }">
          <UButton
            class="mt-2"
            :loading="loading || isProcessing"
            @click.prevent="updateForm"
            label="Update Submission"
          />
        </template>
      </OpenForm>
    </template>
  </UModal>
</template>

<script setup>
import { ref, defineProps, defineEmits, computed } from "vue"
import OpenForm from "../forms/OpenForm.vue"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import { FormMode } from "~/lib/forms/FormModeStrategy.js"
import { useFormManager } from '~/lib/forms/composables/useFormManager'

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  theme: {
      type: Object, default: () => {
        const theme = inject("theme", null)
        if (theme) {
          return theme.value
        }
        return CachedDefaultTheme.getInstance()
      }
    },
  submission: { type: Object },
})

// Modal state
const isModalOpen = computed({
  get() {
    return props.show
  },
  set(value) {
    if (!value) {
      emit("close")
    }
  }
})

// Set up form manager with proper mode
let formManager = null
const setupFormManager = () => {
  if (!props.form) return null
  
  formManager = useFormManager(props.form, FormMode.EDIT)
  
  return formManager
}

// Initialize form manager
formManager = setupFormManager()

watch(() => props.show, (newShow) => {
  if (newShow) {
    nextTick(() => {
      formManager.initialize({
        skipPendingSubmission: true,
        skipUrlParams: true,
        defaultData: props.submission
      })
    })
  }
})

const loading = ref(false)

const emit = defineEmits(["close", "updated"])
const updateForm = () => {
  loading.value = true
  formManager.form.put("/open/forms/" + props.form.id + "/submissions/" + props.submission.id)
    .then((res) => {
      useAlert().success(res.message)
      loading.value = false
      emit("close")
      emit("updated", res.data.data)
    })
    .catch((error) => {
      console.error(error)
      if (error?.data) {
        useAlert().formValidationError(error.data)
      }
      loading.value = false
    })
}

const copySuccess = ref(false)
const { copy } = useClipboard()
const copyToClipboard = () => {
  if (import.meta.server) return

  const url = props.form.share_url + "?submission_id=" + props.submission.submission_id
  copy(url)
  
  // Show success state
  copySuccess.value = true
  
  // Reset after 2 seconds
  setTimeout(() => {
    copySuccess.value = false
  }, 2000)
}
</script>
