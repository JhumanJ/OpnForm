<template>
  <UModal 
    v-model:open="isOpen" 
    :dismissible="false" 
    :ui="{ content: 'sm:max-w-2xl' }"
  >
    <template #header>
      <div class="flex items-center w-full gap-4 px-2">
        <Icon
          :name="integration?.icon"
          class="text-blue-500"
          size="40px"
        />
        <h2 class="text-lg font-semibold">
          {{ integration?.name }}
          <pro-tag v-if="integration?.is_pro === true" />
        </h2>
      </div>
      <UButton
        color="neutral"
        variant="outline"
        icon="i-heroicons-question-mark-circle"
        size="sm"
        @click="openHelp"
      >
        Help
      </UButton>
    </template>

    <template #body>
      <div class="overflow-y-scroll px-2">
        <VForm size="sm">
          <component
            :is="component"
            v-if="integration && component"
            :form="form"
            :integration="integration"
            :integration-data="integrationData"
          />
        </VForm>
      </div>
    </template>

    <template #footer>
      <div class="flex justify-center w-full gap-x-2">
        <UButton
          class="px-8"
          :loading="loading"
          @click.prevent="save"
        >
          Save
        </UButton>
        <UButton
          color="neutral"
          variant="outline"
          class="px-8"
          @click.prevent="isOpen = false"
        >
          Close
        </UButton>
      </div>
    </template>
  </UModal>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  integrationKey: { type: String, required: true },
  integration: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
})

const alert = useAlert()
const emit = defineEmits(["close"])
const loading = ref(false)

// Computed property to handle show/hide logic for UModal
const isOpen = computed({
  get: () => props.show,
  set: (value) => {
    if (!value) {
      emit('close')
    }
  }
})

const crisp = useCrisp()
const openHelp = () => {
  if (props.integration && props.integration?.crisp_help_page_slug) {
    crisp.openHelpdeskArticle(props.integration?.crisp_help_page_slug)
    return
  }
  crisp.openHelpdesk()
}

// Use query composables to get form integrations
const { list } = useFormIntegrations()
const { data: formIntegrationsData } = list(computed(() => props.form.id))

// Get the specific form integration by ID
const formIntegration = computed(() => {
  if (!props.formIntegrationId || !formIntegrationsData.value) return null
  return formIntegrationsData.value.find(integration => integration.id === props.formIntegrationId)
})

const component = computed(() => {
  if (!props.integration) return null
  return resolveComponent(props.integration.file_name)
})

const integrationData = ref(null)

watch(
  () => props.integrationKey,
  () => {
    initIntegrationData()
  },
)

const initIntegrationData = () => {
  integrationData.value = useForm({
    integration_id: props.formIntegrationId && formIntegration.value
      ? formIntegration.value.integration_id
      : props.integrationKey,
    status: props.formIntegrationId && formIntegration.value
      ? formIntegration.value.status === "active"
      : true,
    settings: props.formIntegrationId && formIntegration.value 
      ? formIntegration.value.data ?? {} 
      : {},
    logic: props.formIntegrationId && formIntegration.value
      ? !Array.isArray(formIntegration.value.logic) &&
        formIntegration.value.logic
        ? formIntegration.value.logic
        : null
      : null,
    oauth_id: formIntegration.value?.oauth_id ?? null,
  })
}
initIntegrationData()

const save = () => {
  if (!integrationData.value || loading.value) return
  loading.value = true
  integrationData.value
    .submit(
      props.formIntegrationId ? "PUT" : "POST",
      "/open/forms/{formid}/integration".replace("{formid}", props.form.id) +
        (props.formIntegrationId ? "/" + props.formIntegrationId : ""),
    )
    .then((data) => {
      alert.success(data.message)
      // Invalidate the form integrations query to refetch updated data
      const { invalidateIntegrations } = useFormIntegrations()
      invalidateIntegrations(computed(() => props.form.id))
      emit("close")
    })
    .catch((error) => {
      try {
        alert.error(error.data.message)
      } catch {
        alert.error("An error occurred while saving the integration")
      }
    })
    .finally(() => {
      loading.value = false
    })
}
</script>
