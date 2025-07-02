<template>
  <div class="flex">
    <UButton
        v-track.url_form_prefill_click="{
          form_id: form.id,
          form_slug: form.slug,
        }"
        variant="outline"
        color="neutral"
        icon="i-heroicons-link"
        @click="showUrlFormPrefillModal = true"
        label="URL Pre-fill"
    />

    <UModal
      v-model:open="isModalOpen"
      :ui="{ content: 'sm:max-w-2xl' }"
      title="Url Form Prefill"
    >
      <template #body>
        <div ref="content">
          <p>
            Create dynamic links when sharing your form (whether it's embedded or
            not), that allows you to prefill your form fields. You can use this to
            personalize the form when sending it to multiple contacts for
            instance.
          </p>

          <h3 class="mt-6 border-t text-xl font-semibold mb-4 pt-6">
            How does it work?
          </h3>

          <p>
            Complete your form below and fill only the fields you want to prefill.
            You can even leave the required fields empty.
          </p>

          <div class="rounded-lg p-5 bg-gray-100 dark:bg-gray-900 mt-4">
            <OpenForm
              v-if="formManager"
              :theme="theme"
              :form-manager="formManager"
              @submit="generateUrl"
            >
              <template #submit-btn="{loading}">
                <UButton
                  class="mt-4"
                  :loading="loading"
                  @click="generateUrl"
                  label="Generate Pre-filled URL"
                />
              </template>
            </OpenForm>
          </div>

          <template v-if="prefillFormData">
            <h3 class="mt-6 text-xl font-semibold mb-4 pt-6">
              Your Prefill url
            </h3>
            <FormUrlPrefill
              :form="form"
              :form-data="prefillFormData"
              :extra-query-param="extraQueryParam"
            />
          </template>
        </div>
      </template>
    </UModal>
  </div>
</template>

<script setup>
import ThemeBuilder from "~/lib/forms/themes/ThemeBuilder"
import FormUrlPrefill from "~/components/open/forms/components/FormUrlPrefill.vue"
import OpenForm from "~/components/open/forms/OpenForm.vue"
import { FormMode } from "~/lib/forms/FormModeStrategy.js"
import { useFormManager } from '~/lib/forms/composables/useFormManager'

const props = defineProps({
  form: { type: Object, required: true },
  extraQueryParam: { type: String, default: "" },
})

// State variables
const prefillFormData = ref(null)
const showUrlFormPrefillModal = ref(false)
const content = ref(null)

// Modal state
const isModalOpen = computed({
  get() {
    return showUrlFormPrefillModal.value
  },
  set(value) {
    showUrlFormPrefillModal.value = value
  }
})

// Theme computation
const theme = computed(() => {
  return new ThemeBuilder(props.form.theme, {
    size: props.form.size,
    borderRadius: props.form.border_radius
  }).getAllComponents()
})

// Set up form manager with proper mode
let formManager = null
const setupFormManager = () => {
  if (!props.form) return null
  
  formManager = useFormManager(props.form, FormMode.PREFILL, {
    darkMode: false
  })
  formManager.initialize()
  
  return formManager
}

// Initialize form manager
formManager = setupFormManager()

// Watch for form changes to reinitialize form manager
watch(() => props.form, (newForm) => {
  if (newForm) {
    formManager = setupFormManager()
  } else {
    formManager = null
  }
}, { deep: true })

// Method to generate URL
const generateUrl = () => {
  if (!formManager) return
  
  const formData = formManager.data.value
  
  prefillFormData.value = formData
  
  nextTick().then(() => {
    if (content.value) {
      content.value.parentElement.parentElement.parentElement.scrollTop =
        content.value.offsetHeight
    }
  })
}
</script>
