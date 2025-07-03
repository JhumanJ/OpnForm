<template>
  <SettingsModal
    v-model="isOpen"
    v-model:activeTab="activeTab"
    @close="closeModal"
  >
    <!-- Settings Pages - Auto-register themselves -->
    <SettingsModalPage
      id="information"
      label="Information"
      icon="i-heroicons-information-circle"
    >
      <LazyWorkspacesSettingsInformation />
    </SettingsModalPage>

    <SettingsModalPage
      v-if="!workspace.is_readonly"
      id="domains"
      label="Domains"
      icon="i-heroicons-globe-alt"
    >
      <LazyWorkspacesSettingsDomains />
    </SettingsModalPage>
    
    <SettingsModalPage
      v-if="!workspace.is_readonly"
      id="emails"
      label="Emails"
      icon="i-heroicons-envelope"
    >
      <LazyWorkspacesSettingsEmails />
    </SettingsModalPage>

    <SettingsModalPage
      id="members"
      label="Members"
      icon="i-heroicons-user-group"
    >
      <LazyWorkspacesSettingsMembers />
    </SettingsModalPage>

  </SettingsModal>
</template>

<script setup>
import SettingsModal from '~/components/pages/settings/SettingsModal.vue'
import SettingsModalPage from '~/components/pages/settings/SettingsModalPage.vue'

const emit = defineEmits(['close', 'update:activeTab'])

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  activeTab: {
    type: String,
    default: null
  }
})

const workspace = useWorkspacesStore().getCurrent

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('close', value)
})

// Active tab state
const activeTab = computed({
  get: () => props.activeTab,
  set: (value) => emit('update:activeTab', value)
})

// Methods
const closeModal = () => {
  isOpen.value = false
}
</script> 