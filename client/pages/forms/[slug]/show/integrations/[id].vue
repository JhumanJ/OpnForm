<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <component v-if="integrationProperty.service && integrationProperty.component"
                 :is="integrationProperty.component" :form="form" :service="integrationProperty.service"
        :servicekey="integrationProperty.serviceKey" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import integrations from '~/data/forms/integrations.json'

const props = defineProps({
  form: { type: Object, required: true }
})

definePageMeta({
  middleware: "auth"
})
useOpnSeoMeta({
  title: 'Edit Integration'
})

// Function to find an object by second-level key
function findBySecondKey(obj, secondKey) {
  for (const topLevelKey in obj) {
    if (obj.hasOwnProperty(topLevelKey)) {
      const innerObj = obj[topLevelKey];
      if (innerObj.hasOwnProperty(secondKey)) {
        return innerObj[secondKey];
      }
    }
  }
  return null; // Return null if not found
}

const crisp = useCrisp()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()

const integrationId = computed(() => {
  try {
    return parseInt(useRoute().params.id)
  } catch (error) {
    return null
    console.error("Error getting integrationId", error)
  }
})

const integration = computed(() => formIntegrationsStore.getByKey(integrationId.value))
const integrationProperty = computed(() => {
  if (!integration.value || formIntegrationsStore.loading) return {}
  const service = integration.value?.integration_id ? findBySecondKey(integrations, integration.value.integration_id): null
  return {
    service,
    serviceKey: integration.value.integration_id,
    component: resolveComponent(service?.file_name)
  }
})

onMounted(() => {
  console.log("integration.value", integration.value)
  if (!integration.value) {
    formIntegrationsStore.fetchIntegrations(props.form.id)
  }
})
</script>
