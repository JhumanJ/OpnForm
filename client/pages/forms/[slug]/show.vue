<template>
  <div class="bg-white">
    <template v-if="form">
      <div class="flex bg-gray-50">
        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
          <div class="pt-4 pb-0">
            <a href="#" class="flex text-blue mb-2 font-semibold text-sm" @click.prevent="goBack">
              <svg class="w-3 h-3 text-blue mt-1 mr-1" viewBox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 9L1 5L5 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
              Go back
            </a>

            <div class="flex flex-wrap">
              <h2 class="flex-grow text-gray-900 truncate">
                {{ form.title }}
              </h2>
              <div class="flex">
                <extra-menu class="mr-2" :form="form" />

                <v-button v-if="form.visibility === 'draft'" color="white" class="mr-2 text-blue-600 hidden sm:block"
                  @click="showDraftFormWarningNotification">
                  <svg class="w-6 h-6 inline -mt-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </v-button>
                <v-button v-else v-track.view_form_click="{ form_id: form.id, form_slug: form.slug }" target="_blank"
                  :href="form.share_url" color="white" class="mr-2 text-blue-600 hidden sm:block">
                  <svg class="w-6 h-6 inline -mt-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </v-button>
                <v-button class="text-white" :to="{ name: 'forms-slug-edit', params: { slug: slug } }">
                  <svg class="inline mr-1 -mt-1" width="18" height="17" viewBox="0 0 18 17" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M8.99998 15.6662H16.5M1.5 15.6662H2.89545C3.3031 15.6662 3.50693 15.6662 3.69874 15.6202C3.8688 15.5793 4.03138 15.512 4.1805 15.4206C4.34869 15.3175 4.49282 15.1734 4.78107 14.8852L15.25 4.4162C15.9404 3.72585 15.9404 2.60656 15.25 1.9162C14.5597 1.22585 13.4404 1.22585 12.75 1.9162L2.28105 12.3852C1.9928 12.6734 1.84867 12.8175 1.7456 12.9857C1.65422 13.1348 1.58688 13.2974 1.54605 13.4675C1.5 13.6593 1.5 13.8631 1.5 14.2708V15.6662Z"
                      stroke="currentColor" stroke-width="1.67" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  Edit form
                </v-button>
              </div>
            </div>

            <p class="text-gray-500 text-sm">
              <span class="pr-1">{{ form.views_count }} view{{ form.views_count > 0 ? 's' : '' }}</span>
              <span class="pr-1">- {{ form.submissions_count }}
                submission{{ form.submissions_count > 0 ? 's' : '' }}
              </span>
              <span>- Edited {{ form.last_edited_human }}</span>
            </p>
            <div v-if="['draft', 'closed'].includes(form.visibility) || (form.tags && form.tags.length > 0)"
              class="mt-2 flex items-center flex-wrap gap-3">
              <span v-if="form.visibility == 'draft'"
                class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700">
                Draft - not publicly accessible
              </span>
              <span v-else-if="form.visibility == 'closed'"
                class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700">
                Closed - won't accept new submissions
              </span>
              <span v-for="(tag, i) in form.tags" :key="tag"
                class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:text-white dark:bg-gray-700">
                {{ tag }}
              </span>
            </div>

            <p v-if="form.closes_at" class="text-yellow-500">
              <span v-if="form.is_closed"> This form stopped accepting submissions on the {{
                displayClosesDate
              }} </span>
              <span v-else> This form will stop accepting submissions on the {{ displayClosesDate }} </span>
            </p>
            <p v-if="form.max_submissions_count > 0" class="text-yellow-500">
              <span v-if="form.max_number_of_submissions_reached"> The form is now closed because it reached its limit of
                {{
                  form.max_submissions_count
                }} submissions. </span>
              <span v-else> This form will stop accepting submissions after {{ form.max_submissions_count }} submissions.
              </span>
            </p>

            <form-cleanings class="mt-4" :form="form" />

            <div class="border-b border-gray-200 dark:border-gray-700">
              <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li v-for="(tab, i) in tabsList" :key="i + 1" class="mr-6">
                  <nuxt-link :to="{ name: tab.route }"
                    class="hover:no-underline inline-block py-4 rounded-t-lg border-b-2 text-gray-500 hover:text-gray-600"
                    active-class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500">
                    {{ tab.name }}
                  </nuxt-link>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col bg-white">
        <NuxtPage :form="form" />
      </div>
    </template>
    <div v-else-if="loading" class="text-center w-full p-5">
      <Loader class="h-6 w-6 mx-auto" />
    </div>
    <div v-else class="text-center w-full p-5">
      Form not found.
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import ProTag from '~/components/global/ProTag.vue'
import VButton from '~/components/global/VButton.vue'
import ExtraMenu from '../../../components/pages/forms/show/ExtraMenu.vue'
import FormCleanings from '../../../components/pages/forms/show/FormCleanings.vue'

definePageMeta({
  middleware: "auth"
})
useOpnSeoMeta({
  title: 'Home'
})

const route = useRoute()
const authStore = useAuthStore()
const formsStore = useFormsStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()

const slug = useRoute().params.slug

formsStore.startLoading()
const user = computed(() => authStore.user)
const form = computed(() => formsStore.getByKey(slug))
const workspace = computed(() => workspacesStore.getByKey(form?.value?.workspace_id))
const loading = computed(() => formsStore.loading || workspacesStore.loading)
const displayClosesDate = computed(() => {
  if (form.value && form.value.closes_at) {
    const dateObj = new Date(form.value.closes_at)
    return dateObj.getFullYear() + '-' +
      String(dateObj.getMonth() + 1).padStart(2, '0') + '-' +
      String(dateObj.getDate()).padStart(2, '0') + ' ' +
      String(dateObj.getHours()).padStart(2, '0') + ':' +
      String(dateObj.getMinutes()).padStart(2, '0')
  }
  return ''
})

const tabsList = [
  {
    name: 'Submissions',
    route: 'forms-slug-show-submissions'
  },
  {
    name: 'Integrations',
    route: 'forms-slug-show-integrations'
  },
  {
    name: 'Analytics',
    route: 'forms-slug-show-stats'
  },
  {
    name: 'Share',
    route: 'forms-slug-show-share'
  }
]

onMounted(() => {
  workingFormStore.reset()
  if (form.value) {
    workingFormStore.set(form.value)
  } else {
    formsStore.loadForm(route.params.slug)
  }
})

watch(() => form?.value?.id, (id) => {
  if (id) {
    workingFormStore.set(form.value)
  }
})

const goBack = () => {
  useRouter().push({ name: 'home' })
}

const showDraftFormWarningNotification = () => {
  useAlert().warning('This form is currently in Draft mode and is not publicly accessible, You can change the form status on the edit form page.')
}
</script>
