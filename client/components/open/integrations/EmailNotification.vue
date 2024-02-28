<template>
  <IntegrationWrapper :form="form" v-model="integration">
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
import IntegrationWrapper from './IntegrationWrapper.vue'

const props = defineProps({
  servicekey: { type: String, required: true },
  form: { type: Object, required: false }
})

const alert = useAlert()
const router = useRouter()
const authStore = useAuthStore()
let user = computed(() => authStore.user)
const integration = ref({
  integration_id: props.servicekey,
  settings: {},
  logic: null
})

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
  opnFetch('/open/forms/{id}/integration/create'.replace('{id}', props.form.id), {
    method: 'POST',
    body: integration.value
  }).then(data => {
    alert.success(data.message)
    router.push({ name: 'forms-slug-show-integrations' })
  }).catch((error) => {
    alert.error(error.data.message)
  })
}
</script>
  