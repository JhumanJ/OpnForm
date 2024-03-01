<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <component v-if="integrationProperty.service && integrationProperty.component" :is="integrationProperty.component"
        :form="form" :service="integrationProperty.service" :integrationData="integrationData" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  form: { type: Object, required: true }
})

definePageMeta({
  middleware: "auth"
})
useOpnSeoMeta({
  title: 'New Integration'
})

const crisp = useCrisp()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
formIntegrationsStore.initIntegrations()
const integrations = computed(() => formIntegrationsStore.integrations)
const integrationId = computed(() => useRoute().query.service ?? null)
const integration = computed(() => integrations.value.get(integrationId.value))
const integrationProperty = computed(() => {
  if (!integration.value || formIntegrationsStore.loading) return {}
  const service = integrations.value.get(integrationId.value) ?? null
  return {
    service,
    serviceKey: integrationId.value,
    component: resolveComponent(service.file_name)
  }
})
const integrationData = computed(() => {
  return {
    integration_id: integrationId.value,
    settings: {},
    logic: null
  }
})

</script>
