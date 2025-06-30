<template>
  <UsersSettingsModal
    v-if="authStore.check"
    v-model="isModalOpen"
    v-model:activeTab="appStore.userSettingsModalTab"
    @close="appStore.setUserSettingsModalTab(null)"
    hydrate-on-interaction
  />
  <WorkspacesSettingsModal 
    v-if="authStore.check"
    v-model="isWorkspaceModalOpen"
    v-model:activeTab="appStore.workspaceSettingsModalTab"
    @close="appStore.setWorkspaceSettingsModalTab(null)"
    hydrate-on-interaction
  />
  <WorkspacesSettingsInviteUser 
    v-if="authStore.check"
    v-model="appStore.workspaceInviteUserModal"
    @close="appStore.setWorkspaceInviteUserModal(false)"
  />
</template>

<script setup>
import { watch, computed, onMounted } from "vue"
import { useRouteQuery } from '@vueuse/router'
import { useAppStore } from "~/stores/app"

const appStore = useAppStore()
const authStore = useAuthStore()

const isModalOpen = computed({
  get: () => appStore.userSettingsModalTab !== null,
  set: (value) => {
    if (!value) {
      appStore.setUserSettingsModalTab(null)
    }
  },
})

const isWorkspaceModalOpen = computed({
  get: () => appStore.workspaceSettingsModalTab !== null,
  set: (value) => {
    if (!value) {
      appStore.setWorkspaceSettingsModalTab(null)
    }
  },
})

// Modal state management via URL query parameter
const userSettingsModalQuery = useRouteQuery('user-settings')

// Sync URL -> Store
// Watch the URL parameter and the authentication status.
// This ensures the modal opens if the param is present and the user is logged in.
watch([userSettingsModalQuery, () => authStore.check], ([tab, isLoggedIn]) => {
  if (tab && isLoggedIn) {
    appStore.setUserSettingsModalTab(tab)
  }
  else if (!tab) {
    // Clear tab when param removed
    appStore.setUserSettingsModalTab(null)
  }
}, { immediate: true })

// Sync Store -> URL
// This ensures that when the modal is closed (tab is set to null),
// the parameter is removed from the URL.
watch(() => appStore.userSettingsModalTab, (currentTab) => {
  const newQueryValue = currentTab || undefined
  if (userSettingsModalQuery.value !== newQueryValue) {
    userSettingsModalQuery.value = newQueryValue
  }
})

// Ensure modal opens on first mount if query param already present and user is authenticated
onMounted(() => {
  if (userSettingsModalQuery.value && authStore.check) {
    appStore.setUserSettingsModalTab(userSettingsModalQuery.value)
  }
})
</script> 