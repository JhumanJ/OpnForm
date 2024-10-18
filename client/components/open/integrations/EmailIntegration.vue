<template>
  <IntegrationWrapper
    v-model="props.integrationData"
    :integration="props.integration"
    :form="form"
  >
    <p class="text-gray-500 text-sm mb-3">
      You can <NuxtLink
        class="underline"
        :to="{ name: 'settings-workspace' }"
        target="_blank"
      >
        use our custom SMTP feature
      </NuxtLink> to send emails from your own domain.
    </p>

    <text-area-input
      :form="integrationData"
      name="settings.notification_emails"
      required
      label="Notification Emails"
      help="Add one email per line"
    />
    <text-input
      :form="integrationData"
      name="settings.notification_from_email"
      label="Notification From Email"
      help="Overrides the From email address, if your provider supports it. Leave blank for default."
    />
    <text-input
      :form="integrationData"
      name="settings.notification_reply_to"
      label="Notification Reply To"
      :help="notifiesHelp"
    />
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./components/IntegrationWrapper.vue"

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null },
})

const replayToEmailField = computed(() => {
  const emailFields = props.form.properties.filter((field) => {
    return field.type === "email" && !field.hidden
  })
  if (emailFields.length === 1) return emailFields[0]
  return null
})

const notifiesHelp = computed(() => {
  if (replayToEmailField.value) {
    return (
      'If empty, Reply-to for this notification will be the email filled in the field "' +
      replayToEmailField.value.name +
      '".'
    )
  }
  return "If empty, Reply-to for this notification will be your own email. Add a single email field to your form, and it will automatically become the reply to value."
})
</script>
