import { ref, watch, computed, readonly } from 'vue'
import { useRouteQuery } from '@vueuse/router'
import { createSharedComposable } from '@vueuse/core'
// (No need for explicit imports of auto-imported composables)

// Pre-import modal components so overlay receives component definitions, not promises
import UsersSettingsModal from '~/components/users/settings/Modal.vue'
import WorkspacesSettingsModal from '~/components/workspaces/settings/Modal.vue'

export const useAppModals = createSharedComposable(() => {
  // Internal state management (replaces store)
  const userSettingsTab = ref(null)
  const workspaceSettingsTab = ref(null)
  
  // Get auth store and router helpers (auto-imported by Nuxt)
  const authStore = useAuthStore()
  const route = useRoute()
  const router = useRouter()
  
  // Modal instances using useOverlay
  const overlay = useOverlay()
  const userSettingsModal = overlay.create(UsersSettingsModal, {
    destroyOnClose: false
  })
  const workspaceSettingsModal = overlay.create(WorkspacesSettingsModal, {
    destroyOnClose: false
  })
  
  // URL parameter refs
  const userSettingsQuery = useRouteQuery('user-settings')
  const workspaceSettingsQuery = useRouteQuery('workspace-settings')
  
  // Computed for modal open states
  const isUserSettingsOpen = computed(() => userSettingsTab.value !== null)
  const isWorkspaceSettingsOpen = computed(() => workspaceSettingsTab.value !== null)
  
  // Add flags
  const userSettingsOpen = ref(false)
  const workspaceSettingsOpen = ref(false)
  
  // URL → State sync for user settings
  watch([userSettingsQuery, () => authStore.check], ([tab, isLoggedIn]) => {
    if (tab && isLoggedIn) {
      userSettingsTab.value = tab
      userSettingsOpen.value = true
      userSettingsModal.open({
        activeTab: tab,
        'onUpdate:activeTab': (val) => {
          if (userSettingsOpen.value) {
            userSettingsTab.value = val
          }
        },
        onClose: () => {
          userSettingsOpen.value = false
          userSettingsTab.value = null
          // Immediately remove query param to prevent reopen loop
          const query = { ...route.query }
          delete query['user-settings']
          router.replace({ query })
        }
      })
    } else if (!tab && userSettingsTab.value) {
      userSettingsTab.value = null
      userSettingsModal.close()
    }
  }, { immediate: true })
  
  // URL → State sync for workspace settings
  watch([workspaceSettingsQuery, () => authStore.check], ([tab, isLoggedIn]) => {
    if (tab && isLoggedIn) {
      workspaceSettingsTab.value = tab
      workspaceSettingsOpen.value = true
      workspaceSettingsModal.open({
        activeTab: tab,
        'onUpdate:activeTab': (val) => {
          if (workspaceSettingsOpen.value) {
            workspaceSettingsTab.value = val
          }
        },
        onClose: () => {
          workspaceSettingsOpen.value = false
          workspaceSettingsTab.value = null
          // Immediately remove query param to prevent reopen loop
          const query = { ...route.query }
          delete query['workspace-settings']
          router.replace({ query })
        }
      })
    } else if (!tab && workspaceSettingsTab.value) {
      workspaceSettingsTab.value = null
      workspaceSettingsModal.close()
    }
  }, { immediate: true })
  
  // State → URL sync for user settings
  watch(userSettingsTab, (currentTab) => {
    const query = { ...route.query }
    if (currentTab) {
      query['user-settings'] = currentTab
    } else {
      delete query['user-settings']
    }
    router.replace({ query })
  })
  
  // State → URL sync for workspace settings  
  watch(workspaceSettingsTab, (currentTab) => {
    const query = { ...route.query }
    if (currentTab) {
      query['workspace-settings'] = currentTab
    } else {
      delete query['workspace-settings']
    }
    router.replace({ query })
  })
  
  // Public API
  const openUserSettings = (tab = 'profile', options = {}) => {
    userSettingsTab.value = tab
    userSettingsOpen.value = true
    
    // Update URL unless explicitly disabled
    if (options.updateUrl !== false) {
      userSettingsQuery.value = tab
    }
    
    return userSettingsModal.open({
      activeTab: tab,
      'onUpdate:activeTab': (val) => {
        if (userSettingsOpen.value) {
          userSettingsTab.value = val
        }
      },
      onClose: () => {
        userSettingsOpen.value = false
        userSettingsTab.value = null
        // Immediately remove query param to prevent reopen loop
        const query = { ...route.query }
        delete query['user-settings']
        router.replace({ query })
      }
    })
  }
  
  const openWorkspaceSettings = (tab = 'general', options = {}) => {
    workspaceSettingsTab.value = tab
    workspaceSettingsOpen.value = true
    
    // Update URL unless explicitly disabled
    if (options.updateUrl !== false) {
      workspaceSettingsQuery.value = tab
    }
    
    return workspaceSettingsModal.open({
      activeTab: tab,
      'onUpdate:activeTab': (val) => {
        if (workspaceSettingsOpen.value) {
          workspaceSettingsTab.value = val
        }
      },
      onClose: () => {
        workspaceSettingsOpen.value = false
        workspaceSettingsTab.value = null
        const query = { ...route.query }
        delete query['workspace-settings']
        router.replace({ query })
      }
    })
  }
  
  const closeUserSettings = () => {
    userSettingsTab.value = null
    userSettingsModal.close()
  }
  
  const closeWorkspaceSettings = () => {
    workspaceSettingsTab.value = null
    workspaceSettingsModal.close()
  }
  
  // Handle modal close events from components
  const handleUserSettingsClose = () => {
    userSettingsTab.value = null
  }
  
  const handleWorkspaceSettingsClose = () => {
    workspaceSettingsTab.value = null
  }
  
  return {
    // State (read-only)
    userSettingsTab: readonly(userSettingsTab),
    workspaceSettingsTab: readonly(workspaceSettingsTab),
    isUserSettingsOpen,
    isWorkspaceSettingsOpen,
    
    // Actions
    openUserSettings,
    openWorkspaceSettings,
    closeUserSettings,
    closeWorkspaceSettings,
    
    // Event handlers for modal components
    handleUserSettingsClose,
    handleWorkspaceSettingsClose
  }
}) 