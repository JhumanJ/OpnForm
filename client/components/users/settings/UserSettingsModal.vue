<template>
  <SettingsModal
    v-model="isOpen"
    v-model:activeTab="activeTab"
    title="User Settings"
    subtitle="Manage your personal account preferences and settings"
    header-icon="i-heroicons-user-circle"
    :menu-sections="menuSections"
    @close="closeModal"
    @item-changed="onItemChanged"
  >
    <!-- Account Settings Slot -->
    <template #account="{ item }">
      <AccountSettings />
    </template>

    <!-- Security Settings Slot -->
    <template #security="{ item }">
      <SecuritySettings />
    </template>

    <!-- Connections Settings Slot -->
    <template #connections="{ item }">
      <ConnectionsSettings />
    </template>

    <!-- Access Tokens Settings Slot -->
    <template #access-tokens="{ item }">
      <AccessTokensSettings />
    </template>

    <!-- Billing Settings Slot -->
    <template #billing="{ item }">
      <BillingSettings />
    </template>
  </SettingsModal>
</template>

<script setup>
import SettingsModal from '~/components/pages/settings/SettingsModal.vue'
import AccountSettings from './AccountSettings.vue'
import SecuritySettings from './SecuritySettings.vue'
import ConnectionsSettings from './ConnectionsSettings.vue'
import AccessTokensSettings from './AccessTokensSettings.vue'
import BillingSettings from './BillingSettings.vue'

const emit = defineEmits(['close', 'update:activeTab'])

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  },
  activeTab: {
    type: String,
    default: 'account'
  }
})

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

// Menu structure
const menuSections = computed(() => [
  {
    name: 'User Settings',
    items: [
      {
        id: 'account',
        label: 'Account',
        icon: 'i-heroicons-user'
      },
      {
        id: 'security',
        label: 'Security',
        icon: 'i-heroicons-shield-check'
      },
      {
        id: 'connections',
        label: 'Connections',
        icon: 'i-heroicons-link'
      },
      {
        id: 'access-tokens',
        label: 'Access Tokens',
        icon: 'i-heroicons-key'
      },
      {
        id: 'billing',
        label: 'Billing',
        icon: 'i-heroicons-credit-card'
      }
    ]
  }
])

// Methods
const closeModal = () => {
  isOpen.value = false
}

const onItemChanged = (itemId) => {
  console.log('Active user setting changed to:', itemId)
  // The v-model will handle the tab change automatically
}
</script> 