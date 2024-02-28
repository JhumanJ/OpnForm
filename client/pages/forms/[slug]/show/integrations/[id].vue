<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <component v-if="service && getComponentName" :is="getComponentName" :form="form" :service="service"
        :servicekey="serviceKey" />
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
const route = useRoute()
const router = useRouter()
const formIntegrationsStore = useFormIntegrationsStore()
const integrationId = route.params.id
const integration = computed(() => formIntegrationsStore.getByKey(integrationId))
const integrationProperty = computed(() => {
  return (!integration) ? {} : [serviceKey, integration.integration_id]
})
const serviceKey = ref(integration?.integration_id)
const service = (serviceKey) ? findBySecondKey(integrations, serviceKey) : null
if (!service) {
  // router.push({name:'home'})
}

onMounted(() => {
  console.log("integration.value", integration.value)
  if (!integration.value) {
    formIntegrationsStore.fetchIntegrations(props.form.id)
  }
})

const getComponentName = computed(() => {
  return resolveComponent(service?.file_name)
})

</script>
