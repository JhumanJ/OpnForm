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
  title: 'Edit Integration'
})

const crisp = useCrisp()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
formIntegrationsStore.initIntegrations()
const integrations = computed(() => formIntegrationsStore.integrations)
const integrationId = computed(() => parseInt(useRoute().params.id) ?? null)
const integration = computed(() => formIntegrationsStore.getByKey(integrationId.value))
const integrationProperty = computed(() => {
  if (!integration.value || formIntegrationsStore.loading) return {}
  const service = integrations.value.get(integration.value?.integration_id) ?? null
  return {
    service,
    serviceKey: integration.value.integration_id,
    component: resolveComponent(service.file_name)
  }
})
const integrationData = computed(() => {
  return {
    integration_id: integration.value.integration_id,
    status: (integration.value.status == 1 || integration.value.status == '1') ? true : false,
    settings: integration.value.data ?? {},
    logic: (!Array.isArray(integration.value.logic) && integration.value.logic) ? integration.value.logic : null
  }
})

onMounted(() => {
  if (!integration.value) {
    formIntegrationsStore.fetchFormIntegrations(props.form.id)
  }
})
</script>
