<template>
  <IntegrationWrapper
    v-model="props.integrationData"
    :integration="props.integration"
    :form="form"
  >
    <text-input
      :form="integrationData"
      name="settings.telegram_bot_token"
      label="Telegram Bot Token"
      help="help"
      required
    >
      <template #help>
        <InputHelp>
          <span>
            Receive Telegram messages on each form submission.
            <a
              href="https://core.telegram.org/bots#how-do-i-create-a-bot"
              target="_blank"
              class="text-nt-blue"
            >
              Click here
            </a>
            to learn how to create a Telegram bot and get the token
          </span>
        </InputHelp>
      </template>
    </text-input>

    <text-input
      :form="integrationData"
      name="settings.telegram_chat_id"
      label="Telegram Chat ID"
      help="help"
      required
    >
      <template #help>
        <InputHelp>
          <span>
            The chat ID where messages will be sent. <b>Example: 1234567890</b>
          </span>
        </InputHelp>
      </template>
    </text-input>

    <h4 class="font-bold mt-4">
      Telegram message actions
    </h4>
    <notifications-message-actions
      v-model="integrationData.settings"
      :form="form"
    />
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./components/IntegrationWrapper.vue"
import NotificationsMessageActions from "./components/NotificationsMessageActions.vue"

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null },
})
</script> 