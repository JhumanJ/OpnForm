<template>
  <div v-if="workspace" class="flex flex-col h-full bg-white">
    <!-- Top Bar -->
    <div class="sticky top-0 z-50 bg-white h-[49px] border-b border-neutral-200 p-2 sm:px-4">
      <div class="max-w-4xl mx-auto flex items-center justify-between flex-shrink-0 gap-2 px-2 sm:px-0">
      <div class="flex items-center gap-2">
        <!-- Search -->
        <UInput
          v-if="(forms?.length > 0) || isFilteringForms"
          v-model="search"
          placeholder="Search forms..."
          icon="i-heroicons-magnifying-glass-solid"

        />

        <!-- Tags filter -->
        <USelectMenu
          v-if="allTags.length > 0"
          v-model="selectedTags"
          :items="tagOptions"
          multiple
          placeholder="Tags"
          class="hidden sm:block"
        />

        <!-- Clear button -->
        <UButton
          v-if="isFilteringForms"
          label="Clear"
          variant="ghost"
          color="neutral"
          @click="clearFilters"
        />
      </div>

      <!-- Create form button -->
      <TrackClick name="home_top_bar_create_form_click">
        <UButton
          v-if="!workspace?.is_readonly"
          icon="i-heroicons-plus"
          label="Create Form"
          :to="{ name: 'forms-create' }"
        />
      </TrackClick>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto p-4">
      <div class="max-w-4xl mx-auto">
        <!-- Empty State: No forms -->
        <div v-if="!isFormsLoading && (forms?.length === 0)" class="text-center py-16 px-4">
          <UIcon name="i-heroicons-document-plus" class="h-12 w-12 text-gray-400 mx-auto" />
          <h3 class="mt-4 text-lg font-semibold text-gray-900">
            Create your first form
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            Get started by creating a new form to collect responses.
          </p>
          <UButton
            v-if="!workspace?.is_readonly"
            class="mt-6"
            icon="i-heroicons-plus"
            label="Create Form"
            :to="{ name: 'forms-create' }"
          />
        </div>

        <!-- Empty State: No results -->
        <div v-if="!isFormsLoading && (forms?.length > 0) && enrichedForms.length === 0" class="text-center py-16 px-4">
            <UIcon name="i-heroicons-magnifying-glass" class="h-12 w-12 text-gray-400 mx-auto" />
            <h3 class="mt-4 text-lg font-semibold text-gray-900">
              No forms found
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              Your search and filter criteria did not match any forms.
            </p>
            <UButton
              v-if="isFilteringForms"
              class="mt-6"
              label="Clear Filters"
              variant="soft"
              @click="clearFilters"
            />
        </div>

        <!-- Forms List -->
        <div v-if="enrichedForms.length > 0" class="mb-10">
          <div class="flex flex-col gap-2 lg:mt-6">
            <FormCard
              v-for="form in enrichedForms"
              :key="form.id"
              :form="form"
            />
          </div>

          <!-- Loading Skeletons -->
          <div v-if="isLoadingMore" class="flex flex-col gap-2 mt-2">
            <FormCardSkeleton />
            <FormCardSkeleton class="opacity-60" />
            <FormCardSkeleton class="opacity-30" />
          </div>
          
          <!-- Completion Indicator -->
          <div v-else-if="!isComplete && totalPages > 1" class="flex justify-center items-center py-4">
            <div class="text-sm text-gray-500">
              Loaded {{ currentPage }} of {{ totalPages }} pages
            </div>
          </div>
          
          <div v-if="!workspace?.is_pro" class="px-4">
            <UAlert
              class="mt-8 p-4"
              icon="i-heroicons-sparkles"
              color="primary"
              variant="subtle"
              title="Discover our Pro plan"
            >
              <template #description>
                <div class="flex flex-wrap sm:flex-nowrap gap-4 items-start">
                  <p class="flex-grow">
                    Remove OpnForm branding, customize forms further, use your custom domain, integrate with your favorite tools, invite users, and more!
                  </p>
                  <UButton
                    v-track.upgrade_banner_home_click
                    color="neutral"
                    variant="outline"
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

        <!-- Loading Skeletons -->
        <div v-if="isFormsLoading" class="flex flex-col gap-2">
          <FormCardSkeleton />
          <FormCardSkeleton />
          <FormCardSkeleton />
        </div>
      </div>
    </div>
    <div id="home-portals" class="z-20" />
  </div>
</template>

<script setup>
import Fuse from "fuse.js"
import FormCard from '~/components/pages/home/FormCard.vue'
import FormCardSkeleton from '~/components/pages/home/FormCardSkeleton.vue'

definePageMeta({
  middleware: ["auth", "self-hosted-credentials"],
  layout: "dashboard",
})

useOpnSeoMeta({
  title: "Your Forms",
  description:
    "All of your OpnForm are here. Create new forms, or update your existing forms.",
})

// Composables
const subscriptionModalStore = useSubscriptionModalStore()
const { data: workspace } = useWorkspaces().current()
const {
  forms,
  isLoading: isFormsLoading,
  isFetchingNextPage: isLoadingMore,
  currentPage,
  totalPages,
  isComplete
} = useFormsList(
  workspace.value?.id,
  {
    fetchAll: true,
    enabled: import.meta.client,
  }
)

// State
const search = ref("")
const debouncedSearch = refDebounced(search, 500)
const selectedTags = ref([])

// Methods
const clearFilters = () => {
  search.value = ""
  selectedTags.value = []
}

// Computed
const isFilteringForms = computed(() => {
  return (
    (search.value !== "" && search.value !== null) ||
    selectedTags.value.length > 0
  )
})

// Extract all unique tags from forms
const allTags = computed(() => {
  if (!forms.value) return []
  
  const tagsSet = new Set()
  forms.value.forEach(form => {
    if (form.tags && form.tags.length) {
      form.tags.forEach(tag => tagsSet.add(tag))
    }
  })
  
  return Array.from(tagsSet).sort()
})

const tagOptions = computed(() => allTags.value.map(tag => ({ label: tag, value: tag })))

const enrichedForms = computed(() => {
  if (!forms.value) return []
  
  const enriched = forms.value.filter((form) => {
    if (selectedTags.value.length === 0) {
      return true
    }
    return form.tags && form.tags.length ? selectedTags.value.every(r => form.tags.includes(r.value)) : false
  })

  if (!isFilteringForms.value || search.value === "" || search.value === null) {
    return enriched
  }

  // Fuze search
  const fuzeOptions = {
    keys: ["title", "slug", "tags"],
  }
  const fuse = new Fuse(enriched, fuzeOptions)
  return fuse.search(debouncedSearch.value).map((res) => {
    return res.item
  })
})
</script>
