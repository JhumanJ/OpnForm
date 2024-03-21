<template>
  <modal :show="show" @close="emit('close')">
    <template #icon>
      <Icon :name="integration?.icon" size="40px" />
    </template>
    <template #title>
      {{ integration?.name }}
      <pro-tag v-if="integration?.is_pro === true" />
    </template>

    <div class="p-4">
      <component v-if="integration && component" :is="component" :form="form" :integration="integration"
        :integrationData="integrationData" />
    </div>

    <div class="flex justify-end mt-4 pb-5 px-6">
      <v-button class="mr-2" @click.prevent="save">
        Save
      </v-button>
      <v-button color="white" @click.prevent="emit('close')">
        Close
      </v-button>
    </div>
  </modal>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: { type: Boolean, required: true },
  form: { type: Object, required: true },
  integrationKey: { type: String, required: true },
  integration: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
})

const alert = useAlert()
const emit = defineEmits(['close'])

const formIntegrationsStore = useFormIntegrationsStore()
const formIntegration = computed(() => (props.formIntegrationId) ? formIntegrationsStore.getByKey(props.formIntegrationId) : null)

const component = computed(() => {
  if (!props.integration) return null
  return resolveComponent(props.integration.file_name)
})

const integrationData = ref(computed(() => {
  return {
    integration_id: (props.formIntegrationId) ? formIntegration.value.integration_id : props.integrationKey,
    status: (props.formIntegrationId) ? formIntegration.value.status === 'active' : true,
    settings: (props.formIntegrationId) ? formIntegration.value.data ?? {} : {},
    logic: (props.formIntegrationId) ? (!Array.isArray(formIntegration.value.logic) && formIntegration.value.logic) ? formIntegration.value.logic : null : null
  }
}))

const save = () => {
  opnFetch('/open/forms/{formid}/integration'.replace('{formid}', props.form.id) + ((props.formIntegrationId) ? '/' + props.formIntegrationId : ''), {
    method: (props.formIntegrationId) ? 'PUT' : 'POST',
    body: integrationData.value
  }).then(data => {
    alert.success(data.message)
    formIntegrationsStore.save(data.form_integration)
    emit('close')
  }).catch((error) => {
    alert.error(error.data.message)
  })
}
</script>
