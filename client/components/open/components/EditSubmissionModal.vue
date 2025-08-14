<template>
  <UModal
    v-model:open="isModalOpen"
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
            class="mt-4"
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
</script>
