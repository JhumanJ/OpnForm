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
          <div v-for="(sectionItem, sectionItemKey) in section" :key="sectionItemKey"
               class="bg-gray-50 border border-gray-200 group rounded-md cursor-pointer hover:bg-white transition-colors relative p-4 pb-2 items-center justify-center w-[170px] h-[110px] flex flex-col">
            <div class="flex justify-center">
              <div class="h-10 w-10 text-gray-500 group-hover:text-blue-500 transition-colors flex items-center">
                <Icon :name="sectionItem.icon" size="40px"/>
              </div>
            </div>
            <div class="flex-grow flex items-center">
              <div class="text-gray-400 font-medium text-sm text-center">
                {{ sectionItem.name }}
              </div>
              <pro-tag v-if="sectionItem?.is_pro === true"/>
            </div>
            <NuxtLink v-track.new_integration_click="{ name: sectionItemKey }" class="absolute inset-0"
                      :to="{ name: 'forms-slug-show-integrations-new', query: { 'integration': sectionItemKey } }"/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed} from 'vue'

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
const integrations = computed(() => formIntegrationsStore.integrations)
const sectionsList = computed(() => formIntegrationsStore.integrationsBySection)
const formIntegrationsList = computed(() => formIntegrationsStore.getAllByFormId(props.form.id))

onMounted(() => {
  formIntegrationsStore.fetchFormIntegrations(props.form.id)
})
</script>
