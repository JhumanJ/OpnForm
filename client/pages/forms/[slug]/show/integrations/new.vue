<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">

<!--    <div v-if="!integration.id" v-for="(section, sectionName) in sectionsList" :key="sectionName" class="my-6">-->
<!--      <h3 class="text-gray-500">-->
<!--        {{ sectionName }}-->
<!--      </h3>-->
<!--      <div class="flex flex-wrap mt-4 gap-6">-->
<!--        <div v-for="(sectionItem, sectionItemKey) in section" :key="sectionItemKey"-->
<!--             class="bg-gray-50 border border-gray-200 group rounded cursor-pointer hover:bg-gray-100 relative p-4 pb-2 items-center justify-center w-[170px] h-[120px] flex flex-col">-->
<!--          <div class="flex justify-center">-->
<!--            <div class="h-10 w-10 text-gray-500 group-hover:text-blue-500 transition-colors flex items-center" v-html="sectionItem.icon"/>-->
<!--          </div>-->
<!--          <div class="flex-grow flex items-center">-->
<!--            <div class="text-gray-400 font-medium mt-2 text-sm text-center">-->
<!--              {{ sectionItem.name }}-->
<!--            </div>-->
<!--            <pro-tag v-if="sectionItem?.is_pro === true"/>-->
<!--          </div>-->
<!--          <NuxtLink v-track.new_integration_click="{ name: sectionItemKey }" class="absolute inset-0"-->
<!--                    :to="{ name: 'forms-slug-show-integrations-new', query: { 'service': sectionItemKey } }"/>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

<!--    <div class="mb-20" v-else>-->
<!--      <component v-if="integrationProperty.service && integrationProperty.component" :is="integrationProperty.component"-->
<!--        :form="form" :service="integrationProperty.service" :integrationData="integrationData" />-->
<!--    </div>-->
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
// const integrations = computed(() => formIntegrationsStore.integrations)
// const integrationId = computed(() => useRoute().query.service ?? null)
// const integration = computed(() => integrations.value.get(integrationId.value))
// const integrationProperty = computed(() => {
//   if (!integration.value || formIntegrationsStore.loading) return {}
//   const service = integrations.value.get(integrationId.value) ?? null
//   return {
//     service,
//     serviceKey: integrationId.value,
//     component: resolveComponent(service.file_name)
//   }
// })

const useIntegration = () => {
  const id = ref(null)
  const value = computed(() => formIntegrationsStore.getByKey(id.value))
  const data = computed(() => {
    return {
      integration_id: id.value,
      status: true,
      settings: {},
      logic: null
    }
  })
  watch(() => useRoute().query.integration, (newValue) => {
    if (!newValue || newValue === id.value) return
    id.value = newValue
    console.log('id.value', id.value)
    console.log(value)
  })

  onMounted(() => {
    // Init value
    id.value = useRoute().query.integration
    console.log('in')
    console.log('id.value', id.value)
    console.log(value, value.value)
  })

  return { value, data, id }
}
const integration = useIntegration()
</script>
