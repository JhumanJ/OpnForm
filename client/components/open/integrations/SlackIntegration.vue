<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="integration">
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

const integration = ref(props.integrationData)
</script>
