<template>
  <UDropdownMenu
    v-if="user"
    :items="dropdownItems"
    :ui="{
      content: 'w-56'
    }"
    arrow
  >
    <slot :user="user" />
    
    <template #user-info>
        <div class="flex items-center gap-3">
          <img
            :src="user.photo_url"
            :alt="user.name"
            class="w-8 h-8 rounded-full"
          >
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-neutral-800 truncate">
              {{ user.name }}
            </p>
            <p class="text-xs text-neutral-500 truncate">
              {{ user.email }}
            </p>
          </div>
      </div>
    </template>
  </UDropdownMenu>

  <!-- User Settings Modal -->
  <UserSettingsModal
    v-model="showUserSettings"
    @close="showUserSettings = false"
  />
</template>

<script setup>
import { computed } from "vue"
import UserSettingsModal from "~/components/users/settings/UserSettingsModal.vue"

const authStore = useAuthStore()
const formsStore = useFormsStore()
const workspacesStore = useWorkspacesStore()
const router = useRouter()

const user = computed(() => authStore.user)
const showUserSettings = ref(false)

const logout = async () => {
  // Log out the user.
  authStore.logout()

  // Reset store
  workspacesStore.resetState()
  formsStore.resetState()

  // Redirect to login.
  router.push({ name: "login" })
}

const dropdownItems = computed(() => {
  if (!user.value) return []

  const items = []

  // User info header
  items.push([
    {
      slot: 'user-info',
      type: 'label'
    }
  ])

  // Navigation items
  const navItems = []

  // Settings
  navItems.push({
    label: 'Settings',
    icon: 'i-heroicons-cog-6-tooth',
    onSelect: () => showUserSettings.value = true
  })

  // Admin - only show for moderators
  if (user.value.moderator) {
    navItems.push({
      label: 'Admin',
      icon: 'i-heroicons-shield-check',
      to: { name: 'settings-admin' }
    })
  }

  if (navItems.length > 0) {
    items.push(navItems)
  }

  // Logout
  items.push([
    {
      label: 'Logout',
      icon: 'i-heroicons-arrow-right-start-on-rectangle',
      onSelect: logout
    }
  ])

  return items
})
</script> 