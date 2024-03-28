<template>
  <div class="text-gray-500 border shadow rounded-md p-5 mt-4 relative flex items-center">
    <div class="flex-grow flex items-center">
      <div class="mr-4"
           :class="{ 'text-blue-500': integration.status === 'active', 'text-gray-400': integration.status !== 'active' }">
        <Icon :name="integrationTypeInfo.icon" size="32px"/>
      </div>
      <div>
        <div class="flex space-x-3 font-semibold mr-2">{{ integrationTypeInfo.name }}</div>
        <Badge :color="integration.status === 'active' ? 'green' : 'gray'"
               :before-icon="integration.status === 'active' ? 'solar:play-bold' : 'solar:pause-bold'"
        >
          {{ integration.status === 'active' ? 'Active' : 'Paused' }}
        </Badge>
      </div>
    </div>

    <div v-if="loadingDelete" class="pr-4 pt-2">
      <Loader class="h-6 w-6 mx-auto"/>
    </div>
    <dropdown v-else class="inline">
      <template #trigger="{ toggle }">
        <v-button color="white" @click="toggle">
          <svg class="w-4 h-4 inline -mt-1" viewBox="0 0 16 4" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M8.00016 2.83366C8.4604 2.83366 8.8335 2.46056 8.8335 2.00033C8.8335 1.54009 8.4604 1.16699 8.00016 1.16699C7.53993 1.16699 7.16683 1.54009 7.16683 2.00033C7.16683 2.46056 7.53993 2.83366 8.00016 2.83366Z"
              stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            <path
              d="M13.8335 2.83366C14.2937 2.83366 14.6668 2.46056 14.6668 2.00033C14.6668 1.54009 14.2937 1.16699 13.8335 1.16699C13.3733 1.16699 13.0002 1.54009 13.0002 2.00033C13.0002 2.46056 13.3733 2.83366 13.8335 2.83366Z"
              stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
            <path
              d="M2.16683 2.83366C2.62707 2.83366 3.00016 2.46056 3.00016 2.00033C3.00016 1.54009 2.62707 1.16699 2.16683 1.16699C1.70659 1.16699 1.3335 1.54009 1.3335 2.00033C1.3335 2.46056 1.70659 2.83366 2.16683 2.83366Z"
              stroke="#344054" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </v-button>
      </template>
      <a v-track.edit_form_integration_click="{ form_slug: form.slug, form_integration_id: integration.id }" href="#"
         @click.prevent="showIntegrationModal = true"
         class="flex px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:no-underline hover:text-gray-900 items-center">
        <Icon name="heroicons:pencil" class="w-5 h-5 mr-2"/>
        Edit
      </a>
      <a v-track.past_events_form_integration_click="{ form_slug: form.slug, form_integration_id: integration.id }"
         href="#"
         @click.prevent="showIntegrationEventsModal = true"
         class="flex px-4 py-2 text-md text-gray-700 hover:bg-gray-100 hover:no-underline hover:text-gray-900 items-center">
        <Icon name="heroicons:clock" class="w-5 h-5 mr-2"/>
        Past Events
      </a>
      <a v-track.delete_form_integration_click="{ form_integration_id: integration.id }" href="#"
         class="flex px-4 py-2 text-md text-red-600 hover:bg-red-50 hover:no-underline items-center"
         @click.prevent="deleteFormIntegration(integration.id)">
        <Icon name="heroicons:trash" class="w-5 h-5 mr-2"/>

        Delete Integration
      </a>
    </dropdown>
    <IntegrationModal v-if="form && integration && integrationTypeInfo" :form="form" :integration="integrationTypeInfo"
                      :integrationKey="integration.integration_id" :formIntegrationId="integration.id"
                      :show="showIntegrationModal"
                      @close="showIntegrationModal = false"/>

    <IntegrationEventsModal v-if="form && integration" :form="form" :formIntegrationId="integration.id"
                            :show="showIntegrationEventsModal"
                            @close="showIntegrationEventsModal = false"/>
  </div>
</template>

<script setup>
import {computed} from "vue";

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
    opnFetch('/open/forms/{formid}/integration/{integrationid}'.replace('{formid}', props.form.id).replace('{integrationid}', integrationid), {method: 'DELETE'}).then((data) => {
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
