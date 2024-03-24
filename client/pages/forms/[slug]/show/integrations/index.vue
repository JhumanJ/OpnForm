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
          <div v-for="(sectionItem, sectionItemKey) in section"
               :key="sectionItemKey" role="button" @click="openIntegrationModal(sectionItemKey)"
               v-track.new_integration_click="{ name: sectionItemKey }"
               :class="{'hover:bg-blue-50 group': !sectionItem.coming_soon, 'cursor-not-allowed': sectionItem.coming_soon}"
               class="bg-gray-50 border border-gray-200 rounded-md cursor-pointer transition-colors p-4 pb-2 items-center justify-center w-[170px] h-[110px] flex flex-col relative">
            <div class="flex justify-center">
              <div class="h-10 w-10 text-gray-500 group-hover:text-blue-500 transition-colors flex items-center">
                <Icon :name="sectionItem.icon" size="40px"/>
              </div>
            </div>
            <div class="flex-grow flex items-center">
              <div class="text-gray-400 font-medium text-sm text-center">
                {{ sectionItem.name }}<span v-if="sectionItem.coming_soon"> (coming soon)</span>
              </div>
            </div>
            <pro-tag v-if="sectionItem?.is_pro === true" class="absolute top-0 right-1"/>
          </div>
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
