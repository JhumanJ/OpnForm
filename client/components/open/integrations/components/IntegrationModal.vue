<template>
  <modal
    :show="show"
    compact-header
    :closeable="false"
    @close="emit('close')"
  >
    <template #icon>
      <Icon
        :name="integration?.icon"
        size="40px"
      />
    </template>
    <template #title>
      {{ integration?.name }}
      <pro-tag v-if="integration?.is_pro === true" />
    </template>

    <component
      :is="component"
      v-if="integration && component"
      :form="form"
      :integration="integration"
      :integration-data="integrationData"
    />

    <template #footer>
      <div class="flex justify-center gap-x-2">
        <v-button
          class="px-8"
          :loading="loading"
          @click.prevent="save"
        >
          Save
        </v-button>
        <v-button
          color="white"
          @click.prevent="emit('close')"
        >
          Close
        </v-button>
      </div>
    </template>
  </modal>
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

const formIntegrationsStore = useFormIntegrationsStore()
const formIntegration = computed(() =>
  props.formIntegrationId
    ? formIntegrationsStore.getByKey(props.formIntegrationId)
    : null,
)

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
    integration_id: props.formIntegrationId
      ? formIntegration.value.integration_id
      : props.integrationKey,
    status: props.formIntegrationId
      ? formIntegration.value.status === "active"
      : true,
    settings: props.formIntegrationId ? formIntegration.value.data ?? {} : {},
    logic: props.formIntegrationId
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
      formIntegrationsStore.save(data.form_integration)
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
