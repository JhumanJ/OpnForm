<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="integration">
    <text-input name="webhook_url" v-model="integration.settings.webhook_url" class="mt-4" label="Webhook url"
      help="We will post form submissions to this endpoint" />
  </IntegrationWrapper>
</template>

<script setup>
import IntegrationWrapper from "./components/IntegrationWrapper.vue"

const props = defineProps({
  integration: { type: Object, required: true },
  form: { type: Object, required: true },
  integrationData: { type: Object, required: true },
  formIntegrationId: { type: Number, required: false, default: null }
});

const integration = ref(props.integrationData)
</script>
