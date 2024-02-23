<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <component v-if="getComponentName" :is="getComponentName" :form="form" :service="service"
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
  title: 'New Integration'
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
const serviceKey = route.query?.service ?? null
const service = (serviceKey) ? findBySecondKey(integrations, serviceKey) : null
if (!service) {
  // router.push({name:'home'})
}

const getComponentName = computed(() => {
  return resolveComponent(service.file_name)
})

</script>
