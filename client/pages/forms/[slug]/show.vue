<template>
  <div class="flex flex-col sm:flex-row h-screen bg-white">
    <!-- Form Sidebar - Always shown -->
    <FormSidebar :form="form" :loading="loading" />
    
    <!-- Main content area -->
    <main class="flex-1 sm:pl-58 overflow-hidden">
      <div class="flex flex-col h-full">
        <!-- Loading State -->
        <div v-if="loading" class="flex-1 bg-white">
          <!-- Top Bar Skeleton -->
          <div class="bg-white border-b border-neutral-200 p-4">
            <div class="max-w-4xl mx-auto">
              <!-- Form Title and Actions Skeleton -->
              <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                  <USkeleton class="h-7 w-64 mb-2" />
                  <div class="flex flex-wrap items-center gap-2">
                    <USkeleton class="h-5 w-16" />
                    <USkeleton class="h-5 w-16" />
                    <USkeleton class="h-5 w-32" />
                  </div>
                </div>
                
                <!-- Action Buttons Skeleton -->
                <div class="hidden md:flex gap-2">
                  <USkeleton class="h-9 w-20" />
                  <USkeleton class="h-9 w-20" />
                  <USkeleton class="h-9 w-8" />
                </div>
              </div>
              
              <!-- Status Badges Skeleton -->
              <div class="flex flex-wrap gap-4 mt-3">
                <USkeleton class="h-6 w-16" />
                <USkeleton class="h-6 w-20" />
              </div>
            </div>
          </div>

          <!-- Page Content Skeleton -->
          <div class="flex-1 overflow-y-auto bg-white p-4">
            <div class="max-w-4xl mx-auto space-y-4">
              <USkeleton class="h-8 w-48" />
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <USkeleton class="h-32 w-full" />
                <USkeleton class="h-32 w-full" />
                <USkeleton class="h-32 w-full" />
              </div>
              <USkeleton class="h-64 w-full" />
            </div>
          </div>
        </div>

        <!-- Loaded Content -->
        <template v-else-if="form">
          <!-- Top Bar (Non-Sticky) -->
          <div class="bg-white border-b border-neutral-200 p-4">
            <div class="max-w-4xl mx-auto">
              <!-- Form Title and Actions -->
              <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex-1 min-w-0 hidden sm:block">
                  <h1 class="text-xl font-semibold text-gray-900 truncate ">
                    {{ form.title }}
                  </h1>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2">
                  <UButton
                    v-if="form.visibility === 'draft'"
                    color="neutral"
                    variant="outline"
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
                    color="neutral"
                    variant="outline"
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
                    portal="#form-show-portals"
                  />
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-2 text-gray-500 text-sm mt-1">
                <UTooltip :text="`${formatNumberWithCommas(form.views_count)} views`">
                  <div class="flex items-center gap-1">
                    <UIcon name="i-heroicons-eye" />
                    <span>{{ formatNumber(form.views_count) }}</span>
                  </div>
                </UTooltip>

                <UTooltip :text="`${formatNumberWithCommas(form.submissions_count)} submissions`">
                  <div class="flex items-center gap-1">
                    <UIcon name="i-heroicons-document-text" />
                    <span>{{ formatNumber(form.submissions_count) }}</span>
                  </div>
                </UTooltip>

                <span class="whitespace-nowrap">Edited {{ form.last_edited_human }}</span>
              </div>
              
              <!-- Status Badges and Form Cleanings -->
              <div class="flex flex-wrap gap-4">
                <FormStatusBadges :form="form" />
                <form-cleanings :form="form" />
              </div>
            </div>
          </div>

          <!-- Page Content -->
          <div class="flex-1 overflow-y-auto bg-white">
            <NuxtPage :form="form" />
          </div>
        </template>

        <!-- Not Found State -->
        <div
          v-else
          class="flex items-center justify-center h-screen bg-white"
        >
          <div class="flex flex-col gap-4 items-center justify-center">
            <h2 class="text-lg font-semibold text-gray-900">Form not found</h2>
            <p class="text-gray-500">The form you're looking for doesn't exist or has been deleted.</p>
            <div class="">
            <UButton
              variant="soft"
              class="hover:no-underline"
              icon="i-heroicons-arrow-left"
              :to="{ name: 'home' }"
            >
                Go to Dashboard
              </UButton>
            </div>
          </div>
        </div>

        <div id="form-show-portals" class="z-20" />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from "vue"
import { formatNumber, formatNumberWithCommas } from "~/lib/utils.js"
import FormSidebar from "../../../components/layouts/FormSidebar.vue"
import ExtraMenu from "../../../components/pages/forms/show/ExtraMenu.vue"
import FormCleanings from "../../../components/pages/forms/show/FormCleanings.vue"
import FormStatusBadges from "../../../components/open/forms/components/FormStatusBadges.vue"

definePageMeta({
  layout: "empty",
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

const showDraftFormWarningNotification = () => {
  useAlert().warning(
    "This form is currently in Draft mode and is not publicly accessible, You can change the form status on the edit form page.",
  )
}
</script>
