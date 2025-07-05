<template>
  <nav
    v-if="hasNavbar"
    class="bg-white dark:bg-notion-dark border-b"
  >
    <div class="max-w-7xl mx-auto px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center gap-2">
          <NuxtLink
            :to="{ name: user ? 'home' : 'index' }"
            class="flex-shrink-0 font-semibold hover:no-underline flex items-center"
          >
            <img
              src="/img/logo.svg"
              alt="notion tools logo"
              class="w-8 h-8"
            >
            <span
              class="ml-2 text-md hidden sm:inline text-black dark:text-white"
            >OpnForm</span>
          </NuxtLink>
          <WorkspaceDropdown class="ml-6">
            <template #default="{ workspace }">
              <button
                v-if="workspace"
                class="flex items-center cursor border border-transparent hover:border-gray-200 py-2 px-3 hover:bg-gray-50 rounded-md transition-colors"
              >
                <WorkspaceIcon :workspace="workspace" />
                <p
                  class="hidden md:block max-w-10 truncate text-sm ml-2 text-gray-800 dark:text-gray-200"
                >
                  {{ workspace.name }}
                </p>
              </button>
            </template>
          </WorkspaceDropdown>
        </div>
        <div 
          class="hidden md:flex gap-x-2 ml-auto"
        >
          <NuxtLink
            v-if="$route.name !== 'templates'"
            :to="{ name: 'templates' }"
            :class="navLinkClasses"
          >
            Templates
          </NuxtLink>
          <template v-if="appStore.featureBaseEnabled">
            <button
              v-if="user"
              :class="navLinkClasses"
              @click.prevent="openChangelog"
            >
              What's new? <span
                v-if="hasNewChanges"
                id="fb-update-badge"
                class="bg-blue-500 rounded-full px-2 ml-1 text-white"
              />
            </button>
            <a
              v-else
              :href="opnformConfig.links.changelog_url"
              target="_blank"
              :class="navLinkClasses"
            >
              What's new?
            </a>
          </template>
          <NuxtLink
            v-if="($route.name !== 'ai-form-builder' && user === null) && (!useFeatureFlag('self_hosted') && useFeatureFlag('ai_features'))"
            :to="{ name: 'ai-form-builder' }"
            :class="navLinkClasses"
            class="hidden lg:inline"
          >
            AI Form Builder
          </NuxtLink>
          <NuxtLink
            v-if="
              (useFeatureFlag('billing.enabled') &&
                (user === null || (user && workspace && !workspace.is_pro)) &&
                $route.name !== 'pricing') && !isSelfHosted
            "
            :to="{ name: 'pricing' }"
            :class="navLinkClasses"
          >
            <span
              v-if="user"
              class="text-primary"
            >Upgrade</span>
            <span v-else>Pricing</span>
          </NuxtLink>

          <NuxtLink
            :href="helpUrl"
            :class="navLinkClasses"
            target="_blank"
          >
            Help
          </NuxtLink>
        </div>
        <div
          class="hidden md:block pl-5 border-gray-300 border-r h-5"
        />
        <div
          class="block"
        >
          <div class="flex items-center">
            <div class="ml-4 relative">
              <div class="relative inline-block text-left">
                <UserDropdown v-if="user">
                  <template #default="{ user }">
                    <button
                      id="dropdown-menu-button"
                      type="button"
                      :class="navLinkClasses"
                      class="flex items-center"
                      dusk="nav-dropdown-button"
                    >
                      <img
                        :src="user.photo_url"
                        class="rounded-full w-6 h-6"
                      >
                      <p class="ml-2 hidden sm:inline">
                        {{ user.name }}
                      </p>
                    </button>
                  </template>
                </UserDropdown>
                <div
                  v-else
                  class="flex gap-2"
                >
                  <NuxtLink
                    v-if="$route.name !== 'login'"
                    :to="{ name: 'login' }"
                    :class="navLinkClasses"
                    active-class="text-gray-800 dark:text-white"
                  >
                    Login
                  </NuxtLink>

                  <v-button
                    v-if="!isSelfHosted"
                    v-track.nav_create_form_click
                    size="small"
                    class="shrink-0"
                    :to="{ name: 'forms-create-guest' }"
                    color="outline-blue"
                    :arrow="true"
                  >
                    Create a form
                  </v-button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from '#imports'

import WorkspaceDropdown from './WorkspaceDropdown.vue'
import WorkspaceIcon from '~/components/workspaces/WorkspaceIcon.vue'
import UserDropdown from './UserDropdown.vue'

import opnformConfig from '~/opnform.config.js'
import { useFeatureFlag } from '~/composables/useFeatureFlag'
import { useWorkspaces } from '~/composables/query/useWorkspaces'

// Stores & composables
const authStore = useAuthStore()
const { current } = useWorkspaces()
const { data: workspace } = current()

const appStore = useAppStore()
const formsStore = useFormsStore()

const user = computed(() => authStore.user)
const isIframe = useIsIframe()
const isSelfHosted = computed(() => useFeatureFlag('self_hosted'))
const route = useRoute()

// Constants / classes
const navLinkClasses =
  'border border-transparent hover:border-gray-200 text-gray-500 hover:text-gray-800 hover:no-underline dark:hover:text-white py-2 px-3 hover:bg-gray-50 rounded-md text-sm font-medium transition-colors w-full md:w-auto text-center md:text-left'

// Computed values
const helpUrl = computed(() => opnformConfig.links.help_url)

const form = computed(() => {
  if (route.name && route.name.startsWith('forms-slug')) {
    return formsStore.getByKey(route.params.slug)
  }
  return null
})

const hasNavbar = computed(() => {
  if (isIframe.value) return false

  if (route.name && route.name === 'forms-slug') {
    if (form.value || import.meta.server) {
      return false
    }
    // Form not found/404 case - show the navbar
    return true
  }
  return !appStore.navbarHidden
})

const hasNewChanges = computed(() => {
  if (import.meta.server || !window.Featurebase || !appStore.featureBaseEnabled) return false
  return window.Featurebase('unviewed_changelog_count') > 0
})

// Methods
function openChangelog() {
  if (import.meta.server || !window.Featurebase) return
  window.Featurebase('manually_open_changelog_popup')
}
</script>
