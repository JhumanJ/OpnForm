<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="integration">
    <text-input name="notification_reply_to" v-model="integration.settings.notification_reply_to" class="mt-4"
      label="Notification Reply To" :help="notifiesHelp" />
    <text-area-input name="notification_emails" v-model="integration.settings.notification_emails" class="mt-4"
      label="Notification Emails" help="Add one email per line" />
    <template #submit>
      <v-button @click.prevent="save">
        Save
      </v-button>
    </template>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from './components/IntegrationWrapper.vue'

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
})

const alert = useAlert()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
const integration = ref(props.integrationData)

const replayToEmailField = computed(() => {
  const emailFields = props.form.properties.filter((field) => {
    return field.type === 'email' && !field.hidden
  })
  if (emailFields.length === 1) return emailFields[0]
  return null
})

const notifiesHelp = computed(() => {
  if (replayToEmailField.value) {
    return 'If empty, Reply-to for this notification will be the email filled in the field "' + replayToEmailField.value.name + '".'
  }
  return 'If empty, Reply-to for this notification will be your own email. Add a single email field to your form, and it will automatically become the reply to value.'
})

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
