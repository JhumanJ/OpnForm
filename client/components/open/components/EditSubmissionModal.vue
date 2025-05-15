<template>
  <modal
    :show="show"
    max-width="lg"
    @close="emit('close')"
  >
    <open-form
      v-if="form"
      :form-manager="formManager"
      :theme="theme"
      @submit="updateForm"
    >
      <template #submit-btn="{ loading }">
        <v-button
          :loading="loading"
          class="mt-2 px-8 mx-1"
          @click.prevent="updateForm"
        >
          Update Submission
        </v-button>
      </template>
    </open-form>
  </modal>
</template>

<script setup>
import { ref, defineProps, defineEmits } from "vue"
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
