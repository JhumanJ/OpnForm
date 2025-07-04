<template>
  <UDropdownMenu
    v-model:open="isDropdownOpen"
    v-if="user && workspaces && workspaces.length >= 1"
    :items="dropdownItems"
    :content="content"
    arrow
  >
    <slot :workspace="workspace" />
    
    <template #workspace-info>
      <div>
        <div class="flex items-center gap-3">
          <WorkspaceIcon size="size-8" :workspace="workspace" />
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-neutral-800 truncate">
              {{ workspace.name }}
            </p>
            <p class="text-xs text-neutral-500">
              {{ workspacePlanText }} • {{ memberCountText }}
            </p>
          </div>
        </div>
        <div class="mt-2 flex items-center gap-2">
          <UButton
            icon="i-heroicons-cog-6-tooth"
            size="xs"
            color="neutral"
            variant="outline"
            @click="openSettings"
            label="Settings"
          />
          <UButton
            v-if="workspace.is_admin"
            icon="i-heroicons-user-plus"
            size="xs"
            color="neutral"
            variant="outline"
            @click="openInviteUserModal"
            label="Invite Members"
          />
        </div>
      </div>
    </template>

    <template #item-leading="{ item }">
      <WorkspaceIcon 
        v-if="item.workspace" 
        :workspace="item.workspace" 
        size="size-5"
      />
    </template>
  </UDropdownMenu>

  <!-- Create Workspace Modal -->
  <CreateWorkspaceModal
    v-model="showCreateModal"
    @created="onWorkspaceCreated"
    @close="showCreateModal = false"
  />

  <WorkspacesSettingsInviteUser
    v-model="showInviteUserModal"
    @user-added="onUserAdded"
  />
</template>

<script setup>
import { computed, ref } from "vue"
import WorkspaceIcon from "~/components/workspaces/WorkspaceIcon.vue"
import CreateWorkspaceModal from "~/components/workspaces/CreateWorkspaceModal.vue"
import WorkspacesSettingsInviteUser from '~/components/workspaces/settings/InviteUser.vue'
import { fetchAllWorkspaces } from '~/stores/workspaces.js'

defineProps({
  content: {
    type: Object,
    default: () => ({
      side: 'bottom',
      align: 'start'
    })
  }
})

const subscriptionModalStore = useSubscriptionModalStore()
const authStore = useAuthStore()
const formsStore = useFormsStore()
const workspacesStore = useWorkspacesStore()
const router = useRouter()
const route = useRoute()
const { openWorkspaceSettings } = useAppModals()

const user = computed(() => authStore.user)
const workspaces = computed(() => workspacesStore.getAll)
const workspace = computed(() => workspacesStore.getCurrent)

// Modal state
const showCreateModal = ref(false)
const showInviteUserModal = ref(false)

// Dropdown state
const isDropdownOpen = ref(false)

// Computed text for workspace plan
const workspacePlanText = computed(() => {
  if (!workspace.value) return ''
  return workspace.value.is_pro ? 'Pro Plan' : 'Free Plan'
})

// Computed text for member count
const memberCountText = computed(() => {
  if (!workspace.value || !workspace.value.users_count) return '1 member'
  const count = workspace.value.users_count
  return count === 1 ? '1 member' : `${count} members`
})

const switchWorkspace = (workspaceToSwitch) => {
  workspacesStore.setCurrentId(workspaceToSwitch.id)
  formsStore.resetState()
  formsStore.loadAll(workspaceToSwitch.id)
  
  if (route.name !== "home") {
    router.push({ name: "home" })
  }
}

const createNewWorkspace = () => {
  if (!user.value.is_pro && workspaces.value.length >= 1) {
    subscriptionModalStore.setModalContent('Upgrade to create additional workspaces')
    subscriptionModalStore.openModal()
    return
  }
  showCreateModal.value = true
}

const onWorkspaceCreated = (_newWorkspace) => {
  // Member count is now included in workspace data automatically
}

const onUserAdded = async () => {
  const workspaces = await fetchAllWorkspaces()
  workspacesStore.set(workspaces.data.value.data)
}

const openSettings = () => {
  isDropdownOpen.value = false
  openWorkspaceSettings('information')
}

const openInviteUserModal = () => {
  isDropdownOpen.value = false

  if (workspace.value && !workspace.value.is_pro) {
    subscriptionModalStore.setModalContent('Upgrade to invite users to your workspace')
    subscriptionModalStore.openModal()
    return
  }
  
  showInviteUserModal.value = true
}

const dropdownItems = computed(() => {
  if (!user.value || !workspaces.value) return []

  const items = []

  // Workspace info header (only show if we have a current workspace)
  if (workspace.value) {
    items.push([
      {
        slot: 'workspace-info',
        type: 'label'
      }
    ])
  }

  // Workspace selector section (only show if multiple workspaces)
  if (workspaces.value.length > 1) {
    const workspaceItems = workspaces.value.map(worksp => ({
      label: worksp.name,
      labelClass: workspace.value?.id === worksp?.id ? 'font-medium text-primary-700' : '',
      onSelect: () => switchWorkspace(worksp),
      workspace: worksp // Add workspace data for the slot
    }))

    items.push(workspaceItems)
  }

  // Create workspace action
  items.push([
    {
      label: 'Create Workspace',
      icon: 'i-heroicons-plus',
      onSelect: createNewWorkspace
    }
  ])

  return items
})
</script>

<style></style>
