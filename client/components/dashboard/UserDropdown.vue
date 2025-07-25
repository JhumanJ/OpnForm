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


</template>

<script setup>
import { computed } from "vue"

const { openUserSettings } = useAppModals()

const { user: userQuery, logout: logoutMutationFactory } = useAuth()
const { data: user } = userQuery()

// Create logout mutation
const logoutMutation = logoutMutationFactory()

const logout = () => {
  // Logout mutation handles cache clearing and navigation automatically
  logoutMutation.mutateAsync()
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
    onSelect: () => openUserSettings('account')
  })

  // Admin - only show for moderators
  if (user.value.moderator) {
    navItems.push({
      label: 'Admin',
      icon: 'i-heroicons-shield-check',
      to: { name: 'admin' }
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