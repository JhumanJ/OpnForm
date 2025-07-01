<template>
  <div v-if="form" class="flex h-screen bg-white">
    <!-- Form Sidebar -->
    <FormSidebar :form="form" />
    
    <!-- Main content area -->
    <main class="flex-1 sm:pl-58 overflow-hidden">
      <div class="flex flex-col h-full">
        <!-- Top Bar (Non-Sticky) -->
        <div class="bg-white border-b border-neutral-200 p-4">
          <div class="max-w-4xl mx-auto">
            <!-- Form Title and Actions -->
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <h1 class="text-xl font-semibold text-gray-900 truncate">
                  {{ form.title }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">
                  <span class="pr-1">{{ form.views_count }} view{{
                    form.views_count > 0 ? "s" : ""
                  }}</span>
                  <span class="pr-1">- {{ form.submissions_count }} submission{{
                    form.submissions_count > 0 ? "s" : ""
                  }}
                  </span>
                  <span>- Edited {{ form.last_edited_human }}</span>
                </p>
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
                />
              </div>
            </div>
            
            <!-- Status Badges and Form Cleanings -->
            <div class="mt-3 flex flex-wrap gap-3">
              <FormStatusBadges :form="form" />
              <form-cleanings :form="form" />
            </div>
          </div>
        </div>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto bg-white">
          <NuxtPage :form="form" />
        </div>
      </div>
    </main>
  </div>
  
  <!-- Loading State -->
  <div
    v-else-if="loading"
    class="flex items-center justify-center h-screen bg-white"
  >
    <Loader class="h-6 w-6" />
  </div>
  
  <!-- Not Found State -->
  <div
    v-else
    class="flex items-center justify-center h-screen bg-white"
  >
    <div class="text-center">
      <h2 class="text-lg font-semibold text-gray-900">Form not found</h2>
      <p class="text-gray-500 mt-1">The form you're looking for doesn't exist or has been deleted.</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue"
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
