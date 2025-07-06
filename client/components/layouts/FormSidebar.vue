<template>
  <BaseSidebar>

    <template #mobile-header>
      <h1 class="sm:hidden text-base font-medium text-neutral-500 tracking-wider px-2 truncate">
        {{ loading ? 'Loading...' : form?.title }}
      </h1>
    </template>

    <!-- Navigation Slot -->
    <template #navigation>
      <div 
        v-for="(section, index) in navigationSections" 
        :key="section.name || 'main'"
        :class="[
          index !== navigationSections.length - 1 ? 'mb-6' : '',
          // Push Product and Help sections to bottom
          index === 1 ? 'mt-auto' : ''
        ]"
      >
        <!-- Section Title (if exists) -->
        <h3 
          v-if="section.name"
          class="text-xs font-medium text-neutral-400 tracking-wider mb-2 px-3"
        >
          {{ section.name }}
        </h3>
        
        <!-- Section Items -->
        <ul class="space-y-1">
          <li v-for="item in section.items" :key="item.label">
            <!-- Loading skeleton for form navigation items -->
            <USkeleton 
              v-if="loading && section.name === null && item.label !== 'Dashboard'"
              class="h-9 w-full rounded-md"
            />
            <!-- Regular navigation item -->
            <TrackClick
              v-else
              name="sidebar_nav_click"
              :properties="{ label: item.label, form_id: form?.id }"
            >
              <UButton
                v-bind="item"
                class="w-full justify-start"
                @click="item.onClick"
              />
            </TrackClick>
          </li>
        </ul>
      </div>
    </template>
  </BaseSidebar>
</template>

<script setup>
import BaseSidebar from "~/components/layouts/BaseSidebar.vue"
import TrackClick from '~/components/global/TrackClick.vue'

const props = defineProps({
  form: {
    type: Object,
    required: false
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const route = useRoute()
const { sharedNavigationSections, createNavItem } = useSharedNavigation()

const workspace = useWorkspaces().current

// Check if current route matches a prefix
function isActiveRoute(routeName) {
  if (!routeName) return false
  return route.name === routeName
}

// Form navigation items
const formNavigationItems = computed(() => [
  // Dashboard back button - always show
  createNavItem({
    label: 'Dashboard',
    icon: 'i-heroicons-arrow-left',
    to: { name: 'home' },
    active: false
  }),
  // Show skeleton placeholders when loading, otherwise show real items
  ...(props.loading ? [
    // These will be rendered as skeletons via the template
    createNavItem({
      label: 'Submissions',
      icon: 'i-heroicons-document-text',
      to: '#',
      active: false
    }),
    createNavItem({
      label: 'Integrations',
      icon: 'i-heroicons-puzzle-piece',
      to: '#',
      active: false
    }),
    createNavItem({
      label: 'Analytics',
      icon: 'i-heroicons-chart-bar',
      to: '#',
      active: false
    }),
    createNavItem({
      label: 'Share',
      icon: 'i-heroicons-share',
      to: '#',
      active: false
    })
  ] : [
    createNavItem({
      label: 'Submissions',
      icon: 'i-heroicons-document-text',
      to: { name: 'forms-slug-show-submissions', params: { slug: props.form.slug } },
      active: isActiveRoute('forms-slug-show-submissions')
    }),
    // Hide integrations for read-only workspaces
    ...(workspace.value?.is_readonly ? [] : [createNavItem({
      label: 'Integrations',
      icon: 'i-heroicons-puzzle-piece',
      to: { name: 'forms-slug-show-integrations', params: { slug: props.form.slug } },
      active: isActiveRoute('forms-slug-show-integrations')
    })]),
    createNavItem({
      label: 'Analytics',
      icon: 'i-heroicons-chart-bar',
      to: { name: 'forms-slug-show-stats', params: { slug: props.form.slug } },
      active: isActiveRoute('forms-slug-show-stats')
    }),
    createNavItem({
      label: 'Share',
      icon: 'i-heroicons-share',
      to: { name: 'forms-slug-show-share', params: { slug: props.form.slug } },
      active: isActiveRoute('forms-slug-show-share')
    })
  ])
])

// Navigation sections structure
const navigationSections = computed(() => [
  // Section 1: Form navigation (no name)
  {
    name: null,
    items: formNavigationItems.value
  },
  // Add shared navigation sections (Product and Help)
  ...sharedNavigationSections.value
])
</script> 