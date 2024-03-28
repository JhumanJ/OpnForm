<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="props.integrationData">
    <div>{{ emailSubmissionConfirmationHelp }}</div>

    <div v-if="emailSubmissionConfirmationField">
      <text-input :form="integrationData" name="settings.notification_sender" class="mt-4" required
        label="Confirmation Email Sender Name"
        help="Emails will be sent from our email address but you can customize the name of the Sender" />
      <text-input :form="integrationData" name="settings.notification_subject" class="mt-4" required
        label="Confirmation email subject" help="Subject of the confirmation email that will be sent" />
      <rich-text-area-input :form="integrationData" name="settings.notification_body" class="mt-4" required
        label="Confirmation email content" help="Content of the confirmation email that will be sent" />
      <text-input :form="integrationData" name="settings.confirmation_reply_to" class="mt-4"
                  label="Confirmation Reply To" help="If empty, Reply-to will be your own email."/>
      <toggle-switch-input :form="integrationData" name="settings.notifications_include_submission" class="mt-4"
        label="Include submission data" help="If enabled the confirmation email will contain form submission answers" />
    </div>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./components/IntegrationWrapper.vue"

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
})

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

onBeforeMount(() => {
  for (const [keyname, defaultValue] of Object.entries({
    'notification_sender': 'OpnForm',
    'notification_subject': 'We saved your answers',
    'notification_body': 'Hello there 👋 <br>This is a confirmation that your submission was successfully saved.',
    'notifications_include_submission': true,
  })) {
    if (props.integrationData.settings[keyname] === undefined) {
      props.integrationData.settings[keyname] = defaultValue
    }
  }
})
</script>
