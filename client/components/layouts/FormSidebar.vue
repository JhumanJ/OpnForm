<template>
  <BaseSidebar>
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
            <UButton
              v-track.sidebar_nav_click="item.label"
              v-bind="item"
              class="w-full justify-start"
              @click="item.onClick"
            />
          </li>
        </ul>
      </div>
    </template>
  </BaseSidebar>
</template>

<script setup>
import BaseSidebar from "~/components/layouts/BaseSidebar.vue"

const props = defineProps({
  form: {
    type: Object,
    required: true
  }
})

const route = useRoute()
const workspacesStore = useWorkspacesStore()
const { sharedNavigationSections, createNavItem } = useSharedNavigation()

const workspace = computed(() => workspacesStore.getCurrent)

// Check if current route matches a prefix
function isActiveRoute(routeName) {
  if (!routeName) return false
  return route.name === routeName
}

// Form navigation items
const formNavigationItems = computed(() => [
  // Dashboard back button
  createNavItem({
    label: 'Dashboard',
    icon: 'i-heroicons-arrow-left',
    to: { name: 'home' },
    active: false
  }),
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