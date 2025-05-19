<template>
  <div class="bg-white">
    <template v-if="form">
      <div class="flex bg-gray-50">
        <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl px-4">
          <div class="pt-4 pb-0">
            <a
              href="#"
              class="flex text-blue mb-2 font-semibold text-sm"
              @click.prevent="goBack"
            >
              <svg
                class="w-3 h-3 text-blue mt-1 mr-1"
                viewBox="0 0 6 10"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M5 9L1 5L5 1"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Go back
            </a>

            <div class="flex flex-wrap">
              <h2 class="flex-grow text-gray-900 truncate">
                {{ form.title }}
              </h2>
              <div class="flex mt-4 gap-2 lg:mt-0">
                <UButton
                  v-if="form.visibility === 'draft'"
                  color="white"
                  class="hover:no-underline"
                  icon="i-heroicons-eye"
                  @click="showDraftFormWarningNotification"
                >
                  <span class="hidden sm:inline">View <span class="hidden md:inline">form</span></span>
                </UButton>
                <UButton
                  v-else
                  v-track.view_form_click="{form_id:form.id, form_slug:form.slug}"
                  target="_blank"
                  :to="form.share_url"
                  color="white"
                  class="hover:no-underline"
                  icon="i-heroicons-eye"
                >
                  <span class="hidden sm:inline">View <span class="hidden md:inline">form</span></span>
                </UButton>
                <UButton
                  v-if="!workspace.is_readonly"
                  v-track.edit_form_click="{form_id: form.id, form_slug: form.slug}"
                  color="primary"
                  icon="i-heroicons-pencil"
                  class="hover:no-underline"
                  :to="{ name: 'forms-slug-edit', params: { slug: form.slug } }"
                >
                  Edit <span class="hidden md:inline">form</span>
                </UButton>
                <extra-menu
                  v-if="!workspace.is_readonly"
                  :form="form"
                />
              </div>
            </div>

            <p class="text-gray-500 text-sm">
              <span class="pr-1">{{ form.views_count }} view{{
                form.views_count > 0 ? "s" : ""
              }}</span>
              <span class="pr-1">- {{ form.submissions_count }} submission{{
                form.submissions_count > 0 ? "s" : ""
              }}
              </span>
              <span>- Edited {{ form.last_edited_human }}</span>
            </p>
            
            <FormStatusBadges 
              :form="form"
              class="mt-2"
            />

            <form-cleanings
              class="mt-4"
              :form="form"
            />

            <div class="border-b border-gray-200 dark:border-gray-700">
              <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li
                  v-for="(tab, i) in tabsList"
                  :key="i + 1"
                  class="mr-6"
                >
                  <nuxt-link
                    :to="{ name: tab.route, params: tab.params ?? {} }"
                    class="hover:no-underline inline-block py-4 rounded-t-lg border-b-2 text-gray-500 hover:text-gray-600"
                    active-class="text-blue-600 hover:text-blue-900 dark:text-blue-500 dark:hover:text-blue-500 border-blue-600 dark:border-blue-500"
                  >
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
    <div
      v-else-if="loading"
      class="text-center w-full p-5"
    >
      <Loader class="h-6 w-6 mx-auto" />
    </div>
    <div
      v-else
      class="text-center w-full p-5"
    >
      Form not found.
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
import ExtraMenu from "../../../components/pages/forms/show/ExtraMenu.vue"
import FormCleanings from "../../../components/pages/forms/show/FormCleanings.vue"
import FormStatusBadges from "../../../components/open/forms/components/FormStatusBadges.vue"

definePageMeta({
  middleware: "auth",
})
useOpnSeoMeta({
  title: "Home",
})

const route = useRoute()
const formsStore = useFormsStore()
const workingFormStore = useWorkingFormStore()
const workspacesStore = useWorkspacesStore()

const slug = useRoute().params.slug

formsStore.startLoading()
const form = computed(() => formsStore.getByKey(slug))
const workspace = computed(() => workspacesStore.getCurrent)

const loading = computed(() => formsStore.loading || workspacesStore.loading)

const tabsList = [
  {
    name: "Submissions",
    route: "forms-slug-show-submissions",
    params: { 'slug': slug }
  },
  ...workspace.value.is_readonly ? [] : [
    {
      name: "Integrations",
      route: "forms-slug-show-integrations",
      params: { 'slug': slug }
    },
  ],
  {
    name: "Analytics",
    route: "forms-slug-show-stats",
    params: { 'slug': slug }
  },
  {
    name: "Share",
    route: "forms-slug-show-share",
    params: { 'slug': slug }
  },
]

onMounted(() => {
  workingFormStore.reset()
  if (form.value) {
    workingFormStore.set(form.value)
  } else {
    formsStore.loadForm(route.params.slug)
  }
})

watch(
  () => form?.value?.id,
  (id) => {
    if (id) {
      workingFormStore.set(form.value)
    }
  },
)

const goBack = () => {
  useRouter().push({ name: "home" })
}

const showDraftFormWarningNotification = () => {
  useAlert().warning(
    "This form is currently in Draft mode and is not publicly accessible, You can change the form status on the edit form page.",
  )
}
</script>
