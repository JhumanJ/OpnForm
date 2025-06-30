<template>
  <aside class="fixed left-0 top-0 h-full w-58 bg-neutral-100 border-r border-neutral-200 flex flex-col">
    <!-- Top Section: Workspace (left) and User (right) -->
    <div class="p-2 border-b border-neutral-200">
      <div class="flex items-center justify-between gap-1">
        <!-- Workspace Dropdown -->
        <div class="flex-1 min-w-0">
          <WorkspaceDropdown>
            <template #default="{ workspace }">
              <button
                v-if="workspace"
                class="flex items-center gap-2 p-2 rounded-md hover:bg-neutral-200 transition-colors w-full text-left"
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
    </div>

    <!-- Navigation Sections -->
    <nav class="flex-1 p-2 overflow-y-auto flex flex-col">
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
              v-bind="item"
              class="w-full justify-start"
              @click="item.onClick"
            />
          </li>
        </ul>
      </div>
    </nav>
    <div class="p-2 border-t border-neutral-200">
      <p class="text-xs text-neutral-400 text-center">
        <span class="font-bold">OpnForm</span>
        <span class="text-neutral-500" v-if="version"> v{{ version }}</span>
      </p>
    </div>
  </aside>
</template>

<script setup>
import { computed } from "vue"
import WorkspaceDropdown from "~/components/global/WorkspaceDropdown.vue"
import WorkspaceIcon from "~/components/workspaces/WorkspaceIcon.vue"
import UserDropdown from "~/components/global/UserDropdown.vue"
import opnformConfig from "~/opnform.config.js"

const workspacesStore = useWorkspacesStore()
const appStore = useAppStore()
const route = useRoute()
const crisp = useCrisp()

const workspace = computed(() => workspacesStore.getCurrent)
const isSelfHosted = computed(() => useFeatureFlag('self_hosted'))
const version = computed(() => useFeatureFlag('version'))

// Check if current route matches a prefix
function isActiveRoute(prefix) {
  if (!prefix) return false
  return route.name?.startsWith(prefix)
}

// Check for new changes in changelog
const hasNewChanges = computed(() => {
  if (import.meta.server || !window.Featurebase || !appStore.featureBaseEnabled) return false
  return window.Featurebase("unviewed_changelog_count") > 0
})

// Open changelog modal
function openChangelog() {
  if (import.meta.server || !window.Featurebase) return
  window.Featurebase("manually_open_changelog_popup")
}

// Default button configuration
const defaultButtonProps = {
  variant: 'ghost',
  activeVariant: 'soft', 
  color: 'neutral',
  block: true,
}

// Helper function to apply defaults to navigation items
const createNavItem = (item) => {
  const baseItem = {
    ...defaultButtonProps,
    ...item
  }
  
  // Add custom classes to darken ghost/soft variants for better visibility on neutral-100 background
  const customClasses = ['group']
  
  // For ghost variant (default), darken hover state
  if (baseItem.variant === 'ghost' && baseItem.color === 'neutral') {
    customClasses.push('hover:bg-neutral-200/80')
    baseItem.ui = {
      leadingIcon: 'text-neutral-400 group-hover:text-neutral-500'
    }
  }
  
  // For soft variant (active state), darken background
  if (baseItem.active && baseItem.activeVariant === 'soft' && baseItem.color === 'neutral') {
    customClasses.push('bg-neutral-200/90 text-neutral-800')
  }
  
  // For primary color buttons, ensure good contrast
  if (baseItem.color === 'primary') {
    if (baseItem.variant === 'ghost') {
      customClasses.push('hover:bg-primary-100/80')
    }
    if (baseItem.active && baseItem.activeVariant === 'soft') {
      customClasses.push('data-[active=true]:bg-primary-100/90')
    }
  }
  
  return {
    ...baseItem,
    class: customClasses.length > 0 ? customClasses.join(' ') : undefined
  }
}

// Navigation sections structure
const navigationSections = computed(() => [
  // Section 1: Main navigation (no name)
  {
    name: null,
    items: [
      createNavItem({
        label: 'Create Form',
        icon: 'heroicons:plus',
        to: { name: 'forms-create' },
        active: isActiveRoute('forms-create'),
        color: 'primary',
      }),
      createNavItem({
        label: 'Home', 
        icon: 'heroicons:home',
        to: { name: 'home' },
        active: isActiveRoute('home')
      }),
      createNavItem({
        label: 'Templates',
        icon: 'heroicons:document-duplicate',
        to: { name: 'templates-my-templates' },
        active: isActiveRoute('templates')
      }),
      // Show upgrade for non-pro users
      ...(workspace.value && !workspace.value.is_pro && !isSelfHosted.value ? [createNavItem({
        label: 'Upgrade Plan',
        icon: 'heroicons:sparkles', 
        to: { name: 'pricing' },
        active: isActiveRoute('pricing'),
        color: 'primary' // Override default color
      })] : [])
    ]
  },
  // Section 2: Product
  {
    name: 'Product',
    items: [
      // What's new - only show if feature base enabled
      ...(appStore.featureBaseEnabled ? [createNavItem({
        label: "What's new",
        icon: 'heroicons:megaphone',
        trailingIcon: hasNewChanges.value ? 'heroicons:sparkles' : undefined,
        onClick: openChangelog
      })] : []),
      createNavItem({
        label: 'Roadmap',
        icon: 'heroicons:map',
        to: opnformConfig.links.roadmap,
        target: '_blank'
      }),
      createNavItem({
        label: 'Feature Requests',
        icon: 'heroicons:light-bulb', 
        to: opnformConfig.links.feature_requests,
        target: '_blank'
      })
    ]
  },
  // Section 3: Help
  {
    name: 'Help',
    items: [
      createNavItem({
        label: 'Help Center',
        icon: 'heroicons:question-mark-circle',
        to: opnformConfig.links.help_url,
        target: '_blank'
      }),
      createNavItem({
        label: 'API Docs',
        icon: 'heroicons:code-bracket',
        to: opnformConfig.links.api_docs,
        target: '_blank'
      }),
      ...(isSelfHosted.value || !crisp ? [] : [createNavItem({
        label: 'Contact Support',
        icon: 'heroicons:chat-bubble-left-right',
        onClick: () => crisp.openChat()
      })])
    ]
  }
])
</script> 