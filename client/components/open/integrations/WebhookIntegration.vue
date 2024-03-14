<template>
  <IntegrationWrapper :service="service" :form="form" v-model="integration">
    <text-input name="webhook_url" v-model="integration.settings.webhook_url" class="mt-4" label="Webhook url"
      help="We will post form submissions to this endpoint" />

    <template #submit>
      <v-button @click.prevent="save"> Save </v-button>
    </template>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./IntegrationWrapper.vue"

const props = defineProps({
  service: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
});

const alert = useAlert()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
const formIntegrationId = computed(() => parseInt(useRoute().params.id) ?? null)
const integration = ref(props.integrationData)

const save = () => {
  opnFetch('/open/forms/{formid}/integration'.replace('{formid}', props.form.id) + ((formIntegrationId.value) ? '/' + formIntegrationId.value : ''), {
    method: (formIntegrationId.value) ? 'PUT' : 'POST',
    body: integration.value
  }).then(data => {
    alert.success(data.message)
    formIntegrationsStore.save(data.form_integration)
    router.push({ name: 'forms-slug-show-integrations' })
  }).catch((error) => {
    alert.error(error.data.message)
  })
}
</script>
