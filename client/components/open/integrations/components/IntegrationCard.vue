<template>
  <div class="text-gray-500 border shadow rounded-md p-5 mt-4 relative flex items-center">
    <div class="flex-grow flex items-center">
      <div class="mr-4" :class="{ 'text-blue-500': integration.status === 'active', 'text-gray-400': integration.status !== 'active' }">
        <Icon :name="integrationTypeInfo.icon" size="32px" />
      </div>
      <div>
        <div class="flex space-x-3 font-semibold mr-2">{{ integrationTypeInfo.name }}</div>
        <div v-if="integration.status === 'active'"
          class="rounded-full bg-green-100 border border-green-300 text-green-500 text-xs px-2 inline-flex items-center">
          <span class="bg-green-500 h-[6px] w-[6px] rounded-full inline-block mr-1" />
          Active
        </div>
        <div v-else-if="integration.status === 'inactive'"
          class="rounded-full bg-gray-100 border border-gray-300 text-gray-500 text-xs px-2 inline-flex items-center">
          <span class="bg-gray-500 h-[6px] w-[6px] rounded-full inline-block mr-1" />
          Paused
        </div>
      </div>
    </div>

    <div v-if="loadingDelete" class="pr-4 pt-2">
      <Loader class="h-6 w-6 mx-auto" />
    </div>
    <dropdown v-else class="inline">
      <template #trigger="{ toggle }">
        <v-button color="white" @click="toggle">
          <svg class="w-4 h-4 inline -mt-1" viewBox="0 0 16 4" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M8.00016 2.83366C8.4604 2.83366 8.8335 2.46056 8.8335 2.00033C8.8335 1.54009 8.4604 1.16699 8.00016 1.16699C7.53993 1.16699 7.16683 1.54009 7.16683 2.00033C7.16683 2.46056 7.53993 2.83366 8.00016 2.83366Z"
              stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            <path
              d="M13.8335 2.83366C14.2937 2.83366 14.6668 2.46056 14.6668 2.00033C14.6668 1.54009 14.2937 1.16699 13.8335 1.16699C13.3733 1.16699 13.0002 1.54009 13.0002 2.00033C13.0002 2.46056 13.3733 2.83366 13.8335 2.83366Z"
              stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            <path
              d="M2.16683 2.83366C2.62707 2.83366 3.00016 2.46056 3.00016 2.00033C3.00016 1.54009 2.62707 1.16699 2.16683 1.16699C1.70659 1.16699 1.3335 1.54009 1.3335 2.00033C1.3335 2.46056 1.70659 2.83366 2.16683 2.83366Z"
              stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </v-button>
      </template>
      <a v-track.edit_form_integration_click="{ form_slug: form.slug, form_integration_id: integration.id }" href="#"
        @click.prevent="showIntegrationModal = true"
        class="block block px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center">
        <svg class="w-4 h-4 mr-2" width="18" height="17" viewBox="0 0 18 17" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M8.99998 15.6662H16.5M1.5 15.6662H2.89545C3.3031 15.6662 3.50693 15.6662 3.69874 15.6202C3.8688 15.5793 4.03138 15.512 4.1805 15.4206C4.34869 15.3175 4.49282 15.1734 4.78107 14.8852L15.25 4.4162C15.9404 3.72585 15.9404 2.60656 15.25 1.9162C14.5597 1.22585 13.4404 1.22585 12.75 1.9162L2.28105 12.3852C1.9928 12.6734 1.84867 12.8175 1.7456 12.9857C1.65422 13.1348 1.58688 13.2974 1.54605 13.4675C1.5 13.6593 1.5 13.8631 1.5 14.2708V15.6662Z"
            stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Edit
      </a>
      <a v-track.past_events_form_integration_click="{ form_slug: form.slug, form_integration_id: integration.id }" href="#"
        @click.prevent="showIntegrationEventsModal = true"
        class="block block px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center">
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
        </svg>
        Past Events
      </a>
      <a v-track.delete_form_integration_click="{ form_integration_id: integration.id }" href="#"
        class="block px-4 py-2 text-md text-red-600 hover:bg-red-50 flex items-center"
        @click.prevent="deleteFormIntegration(integration.id)">
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Delete Integration
      </a>
    </dropdown>
    <IntegrationModal v-if="form && integration && integrationTypeInfo" :form="form" :integration="integrationTypeInfo"
      :integrationKey="integration.integration_id" :formIntegrationId="integration.id" :show="showIntegrationModal"
      @close="showIntegrationModal = false" />

    <IntegrationEventsModal v-if="form && integration" :form="form" :formIntegrationId="integration.id" :show="showIntegrationEventsModal"
      @close="showIntegrationEventsModal = false" />
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  integration: {
    type: Object,
    required: true
  },
  form: {
    type: Object,
    required: true
  }
})

const alert = useAlert()
const formIntegrationsStore = useFormIntegrationsStore()
const integrations = computed(() => formIntegrationsStore.availableIntegrations)
const integrationTypeInfo = computed(() => integrations.value.get(props.integration.integration_id))

let showIntegrationModal = ref(false)
let showIntegrationEventsModal = ref(false)
let loadingDelete = ref(false)

const deleteFormIntegration = (integrationid) => {
  alert.confirm('Do you really want to delete this form integration?', () => {
    opnFetch('/open/forms/{formid}/integration/{integrationid}'.replace('{formid}', props.form.id).replace('{integrationid}', integrationid), { method: 'DELETE' }).then((data) => {
      if (data.type === 'success') {
        alert.success(data.message)
        formIntegrationsStore.remove(integrationid)
      } else {
        alert.error('Something went wrong!')
      }
    }).catch((error) => {
      alert.error(error.data.message)
    })
  })
}
</script>
