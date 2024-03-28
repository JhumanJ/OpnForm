<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="props.integrationData">
    <text-input :form="integrationData" name="settings.slack_webhook_url"
                label="Slack webhook url" help="help" required>
      <template #help>
        <InputHelp>
          <template #help>
            <span>
            Receive slack message on each form submission. <a href="https://api.slack.com/messaging/webhooks" target="_blank"> Click here </a>
            to learn how to get a slack webhook url
              </span>
          </template>
        </InputHelp>
      </template>
    </text-input>
    <h4 class="font-bold mt-4">Slack message actions</h4>
    <notifications-message-actions v-model="integrationData.settings"/>
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./components/IntegrationWrapper.vue"
import NotificationsMessageActions from "./components/NotificationsMessageActions.vue"

const props = defineProps({
  integration: {type: Object, required: true},
  form: {type: Object, required: true},
  integrationData: {type: Object, required: true},
  formIntegrationId: {type: Number, required: false, default: null}
})
</script>
