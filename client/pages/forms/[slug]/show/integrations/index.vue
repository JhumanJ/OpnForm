<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <h1 class="font-semibold mt-4 text-xl">
        Your integrations
      </h1>

      <div class="text-sm text-gray-500">
        Read, update and create data with dozens of 3rd-party integrations
      </div>

      <div v-if="integrationsLoading" class="my-6">
        <Loader class="h-6 w-6 mx-auto"/>
      </div>
      <div v-else-if="formIntegrationsList.length" class="my-6">
          <IntegrationCard v-for="(row) in formIntegrationsList" :key="row.id" :integration="row" :form="form"/>
      </div>
      <div class="text-gray-500 border shadow rounded-md p-5 mt-4" v-else>
        No integration yet form this form.
      </div>


      <h1 class="font-semibold mt-8 text-xl">
        Add a new integration
      </h1>
      <div v-for="(section, sectionName) in sectionsList" :key="sectionName" class="mb-8">
        <h3 class="text-gray-500">
          {{ sectionName }}
        </h3>
        <div class="flex flex-wrap mt-2 gap-4">
          <IntegrationListOption v-for="(sectionItem, sectionItemKey) in section"
                                 @select="openIntegrationModal"
                                 :key="sectionItemKey" :integration="sectionItem"/>
        </div>
      </div>
      <IntegrationModal v-if="form && selectedIntegrationKey && selectedIntegration" :form="form"
                        :integration="selectedIntegration" :integrationKey="selectedIntegrationKey"
                        :show="showIntegrationModal"
                        @close="closeIntegrationModal"/>
    </div>
  </div>
</template>

<script setup>
import {computed} from 'vue'
import IntegrationModal from '~/components/open/integrations/components/IntegrationModal.vue'

const props = defineProps({
  form: {type: Object, required: true}
})

definePageMeta({
  middleware: "auth"
})
useOpnSeoMeta({
  title: (props.form) ? 'Form Integrations - ' + props.form.title : 'Form Integrations'
})

const alert = useAlert()
const crisp = useCrisp()
const route = useRoute()

const formIntegrationsStore = useFormIntegrationsStore()
const integrationsLoading = computed(() => formIntegrationsStore.loading)
const integrations = computed(() => formIntegrationsStore.availableIntegrations)
const sectionsList = computed(() => formIntegrationsStore.integrationsBySection)
const formIntegrationsList = computed(() => formIntegrationsStore.getAllByFormId(props.form.id))

let showIntegrationModal = ref(false)
let selectedIntegrationKey = ref(null)
let selectedIntegration = ref(null)

onMounted(() => {
  formIntegrationsStore.fetchFormIntegrations(props.form.id)
})

const openIntegrationModal = (itemKey) => {
  if (!itemKey || !integrations.value.has(itemKey)) return alert.error('Integration not found')
  if (integrations.value.get(itemKey).coming_soon) return alert.warning('This integration is not available yet')
  selectedIntegrationKey.value = itemKey
  selectedIntegration.value = integrations.value.get(selectedIntegrationKey.value)
  showIntegrationModal.value = true
}
const closeIntegrationModal = () => {
  showIntegrationModal.value = false
  nextTick(() => {
    selectedIntegrationKey.value = null
    selectedIntegration.value = null
  })
}
</script>
