<template>
  <IntegrationWrapper :integration="props.integration" :form="form" v-model="props.integrationData">
    <div class="my-5">
      Coming Soon...
    </div>
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
</script>
