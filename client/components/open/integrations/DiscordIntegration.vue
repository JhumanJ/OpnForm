<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="integration">
    <text-input name="discord_webhook_url" v-model="integration.settings.discord_webhook_url" class="mt-4"
      label="Discord webhook url" help="help">
      <template #help>
        Receive a discord message on each form submission.
        <a href="https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks" target="_blank">Click
          here</a> to learn how to get a discord webhook url.
      </template>
    </text-input>
    <h4 class="font-bold mt-4">Discord message actions</h4>
    <form-notifications-message-actions v-model="integration.settings" />

    <template #submit>
      <v-button @click.prevent="save"> Save </v-button>
    </template>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./components/IntegrationWrapper.vue"
import FormNotificationsMessageActions from "~/components/open/forms/components/form-components/components/FormNotificationsMessageActions.vue"

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
});

const alert = useAlert()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
const integration = ref(props.integrationData)

const save = () => {
  opnFetch('/open/forms/{formid}/integration'.replace('{formid}', props.form.id) + ((props.formIntegrationId) ? '/' + props.formIntegrationId : ''), {
    method: (props.formIntegrationId) ? 'PUT' : 'POST',
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
