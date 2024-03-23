<template>
  <div>
    <div v-if="loadingDuplicate || loadingDelete" class="pr-4 pt-2">
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
      <span v-if="isMainPage && form.visibility === 'draft'"
        class="block px-4 py-2 text-md text-gray-700 cursor-pointer dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        @click="showDraftFormWarningNotification">
        <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        View form
      </span>

      <a v-else-if="isMainPage" v-track.view_form_click="{ form_id: form.id, form_slug: form.slug }"
        :href="form.share_url" target="_blank"
        class="block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center">
        <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          <path
            d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        View form
      </a>
      <nuxt-link v-if="isMainPage" v-track.edit_form_click="{ form_id: form.id, form_slug: form.slug }"
        :to="{ name: 'forms-slug-edit', params: { slug: form.slug } }"
        class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center">
        <svg class="w-4 h-4 mr-2" width="18" height="17" viewBox="0 0 18 17" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M8.99998 15.6662H16.5M1.5 15.6662H2.89545C3.3031 15.6662 3.50693 15.6662 3.69874 15.6202C3.8688 15.5793 4.03138 15.512 4.1805 15.4206C4.34869 15.3175 4.49282 15.1734 4.78107 14.8852L15.25 4.4162C15.9404 3.72585 15.9404 2.60656 15.25 1.9162C14.5597 1.22585 13.4404 1.22585 12.75 1.9162L2.28105 12.3852C1.9928 12.6734 1.84867 12.8175 1.7456 12.9857C1.65422 13.1348 1.58688 13.2974 1.54605 13.4675C1.5 13.6593 1.5 13.8631 1.5 14.2708V15.6662Z"
            stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Edit
      </nuxt-link>
      <a v-if="isMainPage" href="#"
        class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        @click.prevent="copyLink">
        <svg class="w-4 h-4 mr-2" viewBox="0 0 16 10" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M6.00016 8.33317H4.66683C2.82588 8.33317 1.3335 6.84079 1.3335 4.99984C1.3335 3.15889 2.82588 1.6665 4.66683 1.6665H6.00016M10.0002 8.33317H11.3335C13.1744 8.33317 14.6668 6.84079 14.6668 4.99984C14.6668 3.15889 13.1744 1.6665 11.3335 1.6665H10.0002M4.66683 4.99984L11.3335 4.99984"
            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        Copy link to share
      </a>
      <a v-track.duplicate_form_click="{ form_id: form.id, form_slug: form.slug }" href="#"
        class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        @click.prevent="duplicateForm">
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
        </svg>
        Duplicate form
      </a>
      <a v-if="!isMainPage" v-track.create_template_click="{ form_id: form.id, form_slug: form.slug }" href="#"
        class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        @click.prevent="showFormTemplateModal = true">
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z" />
        </svg>
        Create Template
      </a>

      <a v-track.change_workspace_click="{ form_id: form.id, form_slug: form.slug }" href="#"
        class="block block px-4 py-2 text-md text-gray-700 dark:text-white hover:bg-gray-100 hover:text-gray-900 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 flex items-center"
        @click.prevent="showFormWorkspaceModal = true">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="w-4 h-4 mr-2">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
        </svg>

        Change workspace
      </a>
      <a v-track.delete_form_click="{ form_id: form.id, form_slug: form.slug }" href="#"
        class="block block px-4 py-2 text-md text-red-600 hover:bg-red-50 flex items-center"
        @click.prevent="showDeleteFormModal = true">
        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
        Delete form
      </a>
    </dropdown>

    <!-- Delete Form Modal -->
    <modal :show="showDeleteFormModal" icon-color="red" max-width="sm" @close="showDeleteFormModal = false">
      <template #icon>
        <svg class="w-10 h-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </template>
      <template #title>
        Delete form
      </template>
      <div class="p-3">
        <p>
          If you want to permanently delete this form and all of its data, you can do so below.
        </p>
        <div class="flex mt-4">
          <v-button class="sm:w-1/2 mr-4" color="white" @click.prevent="showDeleteFormModal = false">
            Cancel
          </v-button>
          <v-button class="sm:w-1/2" color="red" :loading="loadingDelete" @click.prevent="deleteForm">
            Yes, delete it
          </v-button>
        </div>
      </div>
    </modal>
    <form-template-modal v-if="!isMainPage && user" :form="form" :show="showFormTemplateModal"
      @close="showFormTemplateModal = false" />
    <form-workspace-modal v-if="user" :form="form" :show="showFormWorkspaceModal"
      @close="showFormWorkspaceModal = false" />

  </div>
</template>

<script setup>
import { ref, defineProps, computed } from 'vue'
import Dropdown from '~/components/global/Dropdown.vue'
import FormTemplateModal from '../../../open/forms/components/templates/FormTemplateModal.vue'
import FormWorkspaceModal from '../../../open/forms/components/FormWorkspaceModal.vue'

const { copy } = useClipboard()
const router = useRouter()

const props = defineProps({
  form: { type: Object, required: true },
  isMainPage: { type: Boolean, required: false, default: false }
})

const authStore = useAuthStore()
const formsStore = useFormsStore()
const formEndpoint = '/open/forms/{id}'
let user = computed(() => authStore.user)

let loadingDuplicate = ref(false)
let loadingDelete = ref(false)
let showDeleteFormModal = ref(false)
let showFormTemplateModal = ref(false)
let showFormWorkspaceModal = ref(false)

const copyLink = () => {
  copy(props.form.share_url)
  useAlert().success('Copied!')
}
const duplicateForm = () => {
  if (loadingDuplicate.value) return
  loadingDuplicate.value = true
  opnFetch(formEndpoint.replace('{id}', props.form.id) + '/duplicate', { method: 'POST' }).then((data) => {
    formsStore.save(data.new_form)
    router.push({ name: 'forms-slug-show', params: { slug: data.new_form.slug } })
    useAlert().success(data.message)
    loadingDuplicate.value = false
  }).catch((error) => {
    useAlert().error(error.data.message)
    loadingDuplicate.value = false
  })
}
const deleteForm = () => {
  if (loadingDelete.value) return
  loadingDelete.value = true
  opnFetch(formEndpoint.replace('{id}', props.form.id), { method: 'DELETE' }).then((data) => {
    formsStore.remove(props.form)
    router.push({ name: 'home' })
    useAlert().success(data.message)
    loadingDelete.value = false
  }).catch((error) => {
    useAlert().error(error.data.message)
    loadingDelete.value = false
  })
}

const showDraftFormWarningNotification = () => {
  useAlert().warning('This form is currently in Draft mode and is not publicly accessible, You can change the form status on the edit form page.')
}
</script>
