<template>
  <BaseSidebar>
    <!-- Header Slot -->
    <template #header>
      <!-- Workspace Dropdown -->
      <div class="grow">
        <WorkspaceDropdown>
          <template #default="{ workspace }">
            <button
              v-if="workspace"
              class="flex items-center gap-2 p-2 rounded-md hover:bg-neutral-200 transition-colors min-w-32 text-left"
            >
              <WorkspaceIcon :workspace="workspace" />
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-neutral-800 truncate">
                  {{ workspace.name }}
                </p>
              </div>
            </button>
          </template>
        </WorkspaceDropdown>
      </div>
      
      <!-- User Dropdown -->
      <div class="flex gap-2 items-center">
        <UserDropdown>
          <template #default="{ user }">
            <button
              class="flex items-center gap-2 p-2 rounded-md hover:bg-neutral-200 transition-colors"
            >
              <img
                :src="user?.photo_url"
                class="rounded-full w-6 h-6"
                :alt="user?.name"
              >
            </button>
          </template>
        </UserDropdown>
      </div>
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
            <TrackClick
              name="sidebar_nav_click"
              :properties="{ label: item.label }"
            >
              <UButton
                v-bind="item"
                class="w-full justify-start"
                @click="item.onClick"
              >
                <template #trailing v-if="item.kbd">
                  <span class="hidden sm:flex ml-auto">
                    <UKbd v-for="kbd in item.kbd" :key="kbd" :value="kbd" />
                  </span>
                </template>
              </UButton>
            </TrackClick>
          </li>
        </ul>
      </div>
    </template>
  </BaseSidebar>
</template>

<script setup>
import BaseSidebar from "~/components/layouts/BaseSidebar.vue"
import WorkspaceDropdown from "~/components/global/WorkspaceDropdown.vue"
import WorkspaceIcon from "~/components/workspaces/WorkspaceIcon.vue"
import UserDropdown from "~/components/global/UserDropdown.vue"
import { useRouter } from "vue-router"
import TrackClick from "~/components/global/TrackClick.vue"

const route = useRoute()
const router = useRouter()

const { sharedNavigationSections, createNavItem } = useSharedNavigation()

defineShortcuts({
  'n': {
    handler: () => router.push({ name: 'forms-create' }),
  },
})

const workspace = useWorkspaces().current
const isSelfHosted = computed(() => useFeatureFlag('self_hosted'))

// Check if current route matches a prefix
function isActiveRoute(prefix) {
  if (!prefix) return false
  return route.name?.startsWith(prefix)
}

// Navigation sections structure
const navigationSections = computed(() => [
  // Section 1: Main navigation (no name)
  {
    name: null,
    items: [
      createNavItem({
        label: 'Create Form',
        icon: 'i-heroicons-plus',
        to: { name: 'forms-create' },
        active: isActiveRoute('forms-create'),
        color: 'primary',
        variant: 'ghost',
        kbd: ['N'],
      }),
      createNavItem({
        label: 'Home', 
        icon: 'i-heroicons-home',
        to: { name: 'home' },
        active: isActiveRoute('home')
      }),
      createNavItem({
        label: 'Templates',
        icon: 'i-heroicons-document-duplicate',
        to: { name: 'templates-my-templates' },
        active: isActiveRoute('templates')
      }),
      // Show upgrade for non-pro users
      ...(workspace.value && !workspace.value.is_pro && !isSelfHosted.value ? [createNavItem({
        label: 'Upgrade Plan',
        icon: 'i-heroicons-sparkles', 
        to: { name: 'pricing' },
        active: isActiveRoute('pricing'),
        color: 'primary' // Override default color
      })] : [])
    ]
  },
  // Add shared navigation sections (Product and Help)
  ...sharedNavigationSections.value
])
</script> 