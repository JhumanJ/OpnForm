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

      <div v-if="formIntegrationsList.length" class="my-6">
        <h3 class="font-semibold mt-4 text-xl">
          Your connections
        </h3>
        <div v-for="(row) in formIntegrationsList" :key="row.id"
          class="mt-4 flex group bg-white hover:bg-gray-50 dark:bg-notion-dark items-center relative">
          <div
            class="hover:bg-gray-50 bg-white transition shadow-md cursor-pointer border border-gray-200 rounded-lg py-5 pr-10 pl-7 items-center flex w-full group justify-between relative">
            <div class="flex space-x-3">{{ integrations.get(row.integration_id).name }}</div>
            <span v-if="row.status === 'inactive'"
              class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700">
              In-Active
            </span>
            <NuxtLink :to="{ name: 'forms-slug-show-integrations-id', params: { id: row.id } }"
              class="absolute inset-0" />
          </div>
          <div>
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
              <nuxt-link v-track.edit_form_integration_click="{ form_slug: form.slug, form_integration_id: row.id }"
                :to="{ name: 'forms-slug-show-integrations-id', params: { id: row.id } }"
                class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center">
                <svg class="w-4 h-4 mr-2" width="18" height="17" viewBox="0 0 18 17" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M8.99998 15.6662H16.5M1.5 15.6662H2.89545C3.3031 15.6662 3.50693 15.6662 3.69874 15.6202C3.8688 15.5793 4.03138 15.512 4.1805 15.4206C4.34869 15.3175 4.49282 15.1734 4.78107 14.8852L15.25 4.4162C15.9404 3.72585 15.9404 2.60656 15.25 1.9162C14.5597 1.22585 13.4404 1.22585 12.75 1.9162L2.28105 12.3852C1.9928 12.6734 1.84867 12.8175 1.7456 12.9857C1.65422 13.1348 1.58688 13.2974 1.54605 13.4675C1.5 13.6593 1.5 13.8631 1.5 14.2708V15.6662Z"
                    stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Edit
              </nuxt-link>
              <a v-track.delete_form_integration_click="{ form_slug: form.slug, form_integration_id: row.id }" href="#"
                class="block block px-4 py-2 text-md text-red-600 hover:bg-red-50 flex items-center"
                @click.prevent="deleteFormIntegration(row.id)">
                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Integration
              </a>
            </dropdown>
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
              <div class="text-gray-400 font-medium mt-2 text-base">
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

const props = defineProps({
  form: { type: Object, required: true }
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
let loadingDelete = ref(false)
const formIntegrationsStore = useFormIntegrationsStore()
formIntegrationsStore.initIntegrations()
const integrations = computed(() => formIntegrationsStore.integrations)
const sectionsList = computed(() => formIntegrationsStore.integrationsBySection)
const formIntegrationsList = computed(() => formIntegrationsStore.getAllByFormId(props.form.id))

onMounted(() => {
  formIntegrationsStore.fetchFormIntegrations(props.form.id)
})

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
