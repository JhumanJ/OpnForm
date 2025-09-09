<template>
  <UModal
    v-model:open="isModalOpen"
    :ui="{ content: 'sm:max-w-2xl' }"
    title="Edit Submission"
  >
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
            :loading="updateSubmissionMutation.isPending.value || isProcessing"
            @click.prevent="updateForm"
            label="Update Submission"
          />
        </template>
      </OpenForm>
    </template>
  </UModal>
</template>

<script setup>
import { defineProps, defineEmits, computed } from "vue"
import OpenForm from "../forms/OpenForm.vue"
import CachedDefaultTheme from "~/lib/forms/themes/CachedDefaultTheme.js"
import { FormMode } from "~/lib/forms/FormModeStrategy.js"
import { useFormManager } from '~/lib/forms/composables/useFormManager'
import { useFormSubmissions } from "~/composables/query/forms/useFormSubmissions"

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

// Use form submissions composable for update
const { updateSubmission } = useFormSubmissions()
const updateSubmissionMutation = updateSubmission()

const emit = defineEmits(["close"])
const alert = useAlert()

const updateForm = () => {
  updateSubmissionMutation.mutate(
    {
      formId: props.form.id,
      submissionId: props.submission.id,
      data: formManager.form.data()
    },
    {
      onSuccess: (res) => {
        alert.success(res.message)
        emit("close")
      },
      onError: (error) => {
        console.error(error)
        if (error?.data) {
          alert.formValidationError(error.data)
        }
      }
    }
  )
}
</script>
