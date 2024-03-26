<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="props.integrationData">
    <text-input :form="integrationData" name="settings.webhook_url" class="mt-4" label="Webhook url"
                help="We will post form submissions to this endpoint" required />
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
</script>
