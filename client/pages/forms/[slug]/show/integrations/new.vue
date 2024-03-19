<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <component v-if="integration && component" :is="component"
                 :form="form" :integration="integration" :integrationData="integrationData" />
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
const integrationId = computed(() => useRoute().query.integration ?? null)
const integration = computed(() => integrations.value.get(integrationId.value))
const component = computed(() => {
  if (!integration.value) return null
  return resolveComponent(integration.value.file_name)
})
const integrationData = computed(() => {
  return {
    integration_id: integrationId.value,
    status: true,
    settings: {},
    logic: null
  }
})

</script>
