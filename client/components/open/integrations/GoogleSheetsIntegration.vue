<template>
  <IntegrationWrapper
    v-model="props.integrationData"
    :integration="props.integration"
    :form="form"
  >
    <div class="my-5">
      <select-input
        v-model="integrationData.oauth_id"
        name="provider"
        :options="providers"
        option-key="id"
        emit-key="id"
        :required="true"
        label="Select Google Account"
      />
    </div>
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

const oAuthProvidersStore = useOAuthProvidersStore()
const providers = computed(() => oAuthProvidersStore.getAll.filter(provider => provider.provider == 'google'))
</script>
