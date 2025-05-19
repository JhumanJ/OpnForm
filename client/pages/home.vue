<template>
  <div
    v-if="workspace"
    class="bg-white"
  >
    <div class="flex bg-gray-50 pb-5 border-b">
      <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl p-4">
        <div class="pt-4 pb-0">
          <div class="flex">
            <h2 class="flex-grow text-gray-900">
              Your Forms
            </h2>
            <v-button
              v-if="!workspace?.is_readonly"
              v-track.create_form_click
              :to="{ name: 'forms-create' }"
            >
              <svg
                class="w-4 h-4 text-white inline mr-1 -mt-1"
                viewBox="0 0 14 14"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M6.99996 1.1665V12.8332M1.16663 6.99984H12.8333"
                  stroke="currentColor"
                  stroke-width="1.67"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Create a new form
            </v-button>
          </div>
          <small class="flex text-gray-500">Manage your forms and submissions.</small>
        </div>
      </div>
    </div>
    <div class="flex bg-white">
      <div class="w-full md:w-4/5 lg:w-3/5 md:mx-auto md:max-w-4xl">
        <div class="mt-4 pb-0">
          <text-input
            v-if="forms.length > 0"
            v-model="search"
            class="mb-6 px-4"
            name="search"
            label="Search a form"
            placeholder="Name of form to search"
          />
          <div
            v-if="allTags.length > 0"
            class="mb-4 px-6"
          >
            <div
              v-for="tag in allTags"
              :key="tag"
              :class="[
                'inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset cursor-pointer mr-2',
                {
                  'bg-blue-50 text-blue-600 ring-blue-500/10 dark:bg-blue-400':
                    selectedTags.has(tag),
                  'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-700 hover:bg-blue-50 hover:text-blue-600 hover:ring-blue-500/10 hover:dark:bg-blue-400':
                    !selectedTags.has(tag),
                },
              ]"
              title="Click for filter by tag(s)"
              @click="onTagClick(tag)"
            >
              {{ tag }}
            </div>
          </div>
          <div
            v-if="!formsLoading && enrichedForms.length === 0"
            class="flex flex-wrap justify-center max-w-4xl"
          >
            <img
              class="w-56"
              src="/img/pages/forms/search_notfound.png"
              alt="search-not-found"
            >

            <h3 class="w-full mt-4 text-center text-gray-900 font-semibold">
              No forms found
            </h3>
            <div
              v-if="isFilteringForms && enrichedForms.length === 0 && search"
              class="mt-2 w-full text-center"
            >
              Your search "{{ search }}" did not match any forms. Please try
              again.
            </div>
            <v-button
              v-if="!workspace?.is_readonly && forms.length === 0"
              v-track.create_form_click
              class="mt-4"
              :to="{ name: 'forms-create' }"
            >
              <svg
                class="w-4 h-4 text-white inline mr-1 -mt-1"
                viewBox="0 0 14 14"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M6.99996 1.1665V12.8332M1.16663 6.99984H12.8333"
                  stroke="currentColor"
                  stroke-width="1.67"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
              Create a new form
            </v-button>
          </div>
          <div
            v-else-if="forms.length > 0"
            class="mb-10"
          >
            <div v-if="enrichedForms && enrichedForms.length">
              <div
                v-for="(form) in enrichedForms"
                :key="form.id"
                class="mt-4 p-4 flex group bg-white hover:bg-gray-50 dark:bg-notion-dark items-center relative"
              >
                <div
                  class="flex-grow items-center truncate cursor-pointer relative"
                >
                  <NuxtLink
                    :to="{name:'forms-slug-show-submissions', params: {slug:form.slug}}"
                    class="absolute inset-0"
                  />
                  <span class="font-semibold text-gray-900 dark:text-white">{{
                    form.title
                  }}</span>
                  <ul class="flex text-gray-500 text-sm gap-4">
                    <li class="pr-1 mr-3">
                      {{ form.views_count }} view{{
                        form.views_count > 0 ? "s" : ""
                      }}
                    </li>
                    <li class="list-disc mr-3">
                      {{ form.submissions_count }}
                      submission{{ form.submissions_count > 0 ? "s" : "" }}
                    </li>
                    <li class="list-disc mr-3">
                      Edited {{ form.last_edited_human }}
                    </li>
                    <li
                      v-if="form.creator"
                      class="list-disc hidden lg:list-item"
                    >
                      By
                      {{ form?.creator?.name }}
                    </li>
                  </ul>
                  
                  <FormStatusBadges
                    :form="form"
                    class="mt-1"
                    size="xs"
                  />
                  
                </div>
                <extra-menu
                  :form="form"
                  :is-main-page="true"
                />
              </div>
            </div>
            <div
              v-if="!workspace?.is_pro"
              class="px-4"
            >
              <UAlert
                class="mt-8 p-4"
                icon="i-heroicons-sparkles"
                color="primary"
                variant="subtle"
                description="You can add components to your app using the cli."
              >
                <template #title>
                  <h3 class="font-semibold text-md">
                    Discover our Pro plan
                  </h3>
                </template>
                <template #description>
                  <div class="flex flex-wrap sm:flex-nowrap gap-4 items-start">
                    <p class="flex-grow">
                      Remove OpnForm branding, customize forms further, use your custom domain, integrate with your
                      favorite tools, invite users, and more!
                    </p>
                    <UButton
                      v-track.upgrade_banner_home_click
                      color="white"
                      class="block"
                      @click.prevent="subscriptionModalStore.openModal()"
                    >
                      Upgrade Now
                    </UButton>
                  </div>
                </template>
              </UAlert>
            </div>
          </div>
          <div
            v-if="formsLoading"
            class="text-center"
          >
            <Loader class="h-6 w-6 text-nt-blue mx-auto" />
          </div>
        </div>
      </div>
    </div>
    <open-form-footer class="mt-8 border-t" />
  </div>
</template>

<script setup>
import {useFormsStore} from "../stores/forms"
import {useWorkspacesStore} from "../stores/workspaces"
import Fuse from "fuse.js"
import TextInput from "../components/forms/TextInput.vue"
import ExtraMenu from "../components/pages/forms/show/ExtraMenu.vue"
import FormStatusBadges from "../components/open/forms/components/FormStatusBadges.vue"
import {refDebounced} from "@vueuse/core"

definePageMeta({
  middleware: ["auth", "self-hosted-credentials"],
})

useOpnSeoMeta({
  title: "Your Forms",
  description:
    "All of your OpnForm are here. Create new forms, or update your existing forms.",
})

const subscriptionModalStore = useSubscriptionModalStore()
const formsStore = useFormsStore()
const workspacesStore = useWorkspacesStore()
formsStore.startLoading()

const workspace = computed(() => workspacesStore.getCurrent)

onMounted(() => {
  if (!formsStore.allLoaded) {
    formsStore.loadAll(workspacesStore.currentId)
  } else {
    formsStore.stopLoading()
  }
})

// State
const {
  getAll: forms,
  loading: formsLoading,
  allTags,
} = storeToRefs(formsStore)
const search = ref("")
const debouncedSearch = refDebounced(search, 500)
const selectedTags = ref(new Set())

// Methods

const onTagClick = (tag) => {
  if (selectedTags?.value?.has(tag)) {
    selectedTags.value.delete(tag)
  } else {
    selectedTags.value.add(tag)
  }
}

// Computed
const isFilteringForms = computed(() => {
  return (
    (search.value !== "" && search.value !== null) ||
    selectedTags.value.size > 0
  )
})

const enrichedForms = computed(() => {
  const enrichedForms = forms.value.map((form) => {
    form.workspace = workspacesStore.getByKey(form.workspace_id)
    return form
  }).filter((form) => {
    if (selectedTags.value.size === 0) {
      return true
    }
    return form.tags && form.tags.length ? [...selectedTags.value].every(r => form.tags.includes(r)) : false
  })

  if (!isFilteringForms || search.value === "" || search.value === null) {
    return enrichedForms
  }

  // Fuze search
  const fuzeOptions = {
    keys: ["title", "slug", "tags"],
  }
  const fuse = new Fuse(enrichedForms, fuzeOptions)
  return fuse.search(debouncedSearch.value).map((res) => {
    return res.item
  })
})
</script>
