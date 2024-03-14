<template>
  <IntegrationWrapper :service="service" :form="form" v-model="integration">
    <div class="mt-5">{{ emailSubmissionConfirmationHelp }}</div>

    <div v-if="emailSubmissionConfirmationField">
      <text-input name="confirmation_reply_to" v-model="integration.settings.confirmation_reply_to" class="mt-4"
        label="Confirmation Reply To" help="help">
        <template #help>
          If empty, Reply-to will be your own email.
        </template>
      </text-input>
      <text-input name="notification_sender" v-model="integration.settings.notification_sender" class="mt-4"
        label="Confirmation Email Sender Name"
        help="Emails will be sent from our email address but you can customize the name of the Sender" />
      <text-input name="notification_subject" v-model="integration.settings.notification_subject" class="mt-4"
        label="Confirmation email subject" help="Subject of the confirmation email that will be sent" />
      <rich-text-area-input name="notification_body" v-model="integration.settings.notification_body" class="mt-4"
        label="Confirmation email content" help="Content of the confirmation email that will be sent" />
      <toggle-switch-input name="notifications_include_submission"
        v-model="integration.settings.notifications_include_submission" class="mt-4" label="Include submission data"
        help="If enabled the confirmation email will contain form submission answers" />
    </div>

    <template #submit v-if="emailSubmissionConfirmationField">
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

const emailSubmissionConfirmationField = computed(() => {
  if (!props.form.properties || !Array.isArray(props.form.properties)) return null
  const emailFields = props.form.properties.filter((field) => {
    return field.type === 'email' && !field.hidden
  })
  if (emailFields.length === 1) return emailFields[0]
  return null
})
const emailSubmissionConfirmationHelp = computed(() => {
  if (emailSubmissionConfirmationField.value) {
    return 'Confirmation will be sent to the email in the "' + emailSubmissionConfirmationField.value.name + '" field.'
  }
  return 'Only available if your form contains 1 email field.'
})

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
