<template>
  <modal
    :show="show"
    max-width="lg"
    @close="emit('close')"
  >
    <open-form
      :theme="theme"
      :loading="false"
      :form="form"
      :fields="form.properties"
      :default-data-form="submission"
      :mode="FormMode.EDIT"
      @submit="updateForm"
    >
      <template #submit-btn="{ submitForm }">
        <v-button
          :loading="loading"
          class="mt-2 px-8 mx-1"
          @click.prevent="submitForm"
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

const loading = ref(false)

const emit = defineEmits(["close", "updated"])
const updateForm = (form, onFailure) => {
  loading.value = true
  form
    .put("/open/forms/" + props.form.id + "/submissions/" + props.submission.id)
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
      onFailure()
    })
}
</script>
