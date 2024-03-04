<template>
  <IntegrationWrapper :service="service" :form="form" v-model="integration">
    <text-input name="slack_webhook_url" v-model="integration.settings.slack_webhook_url" class="mt-4"
      label="Slack webhook url" help="help">
      <template #help>
        Receive slack message on each form submission.
        <a href="https://api.slack.com/messaging/webhooks" target="_blank">Click here</a>
        to learn how to get a slack webhook url
      </template>
    </text-input>
    <h4 class="font-bold mt-4">Slack message actions</h4>
    <form-notifications-message-actions v-model="integration.settings" />

    <template #submit>
      <v-button @click.prevent="save"> Save </v-button>
    </template>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./IntegrationWrapper.vue"
import FormNotificationsMessageActions from "~/components/open/forms/components/form-components/components/FormNotificationsMessageActions.vue"

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
