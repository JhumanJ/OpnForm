<template>
  <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
    <div class="mb-20">
      <h3 class="font-semibold mt-4 text-xl">
        Connect your form to 3rd-party apps
      </h3>
      <div>
        Read, update and create data with dozens of integrations
        <a class="cursor-pointer ml-1" @click.prevent="crisp.openHelpdesk()">Need Help?</a>
      </div>

      <div v-if="integrationsList.length" class="my-6">
        <h3 class="font-semibold mt-4 text-xl">
          Your connections
        </h3>
        <div v-for="(row) in integrationsList" :key="row.id" class="space-y-4 pr-7">
          <div
            class="hover:bg-gray-50 bg-white transition shadow-md cursor-pointer border border-gray-200 rounded-lg py-5 pr-10 pl-7 items-center flex w-full group justify-between relative">
            <div class="flex space-x-3">{{ row.integration_id }}</div>
            <NuxtLink :to="{ name: 'forms-slug-show-integrations-id', params: { id: row.id } }"
              class="absolute inset-0" />
          </div>
        </div>
      </div>

      <div v-for="(section, sectionName) in sectionsList" :key="sectionName" class="my-6">
        <h3 class="mb-1 text-xl text-gray-600">
          {{ sectionName }}
        </h3>
        <div class="flex flex-wrap gap-y-6 mt-[10px]">
          <div v-for="(sectionItem, sectionItemKey) in section" :key="sectionItemKey" class="mr-6">
            <div
              class="bg-gray-50 border border-gray-200 rounded cursor-pointer hover:bg-gray-100 relative w-[180px] h-[150px] p-4 pb-2 items-center justify-center">
              <div v-html="sectionItem.icon"></div>
              <div class="text-gray-400 font-medium mt-4 text-base whitespace-nowrap">
                {{ sectionItem.name }}
              </div>
              <pro-tag v-if="sectionItem?.is_pro === true" />
              <NuxtLink v-track.new_integration_click="{ name: sectionItemKey }" class="absolute inset-0"
                :to="{ name: 'forms-slug-show-integrations-new', query: { 'service': sectionItemKey } }" />
            </div>
          </div>
        </div>
      </div>
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
  title: (props.form) ? 'Form Integrations - ' + props.form.title : 'Form Integrations'
})

const crisp = useCrisp()
const route = useRoute()
const sectionsList = computed(() => integrations)
const formIntegrationsStore = useFormIntegrationsStore()
const integrationsList = computed(() => formIntegrationsStore.getAllByFormId(props.form.id))

onMounted(() => {
  formIntegrationsStore.fetchIntegrations(props.form.id)
})
</script>
