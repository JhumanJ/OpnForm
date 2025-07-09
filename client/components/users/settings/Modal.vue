<template>
  <SettingsModal
    v-model="isOpen"
    v-model:activeTab="activeTab"
    @close="closeModal"
  >
    <!-- Settings Pages - Auto-register themselves -->
    <SettingsModalPage
      id="account"
      label="Account"
      icon="i-heroicons-user"
    >
      <LazyUsersSettingsAccount />
    </SettingsModalPage>

    <SettingsModalPage
      id="security"
      label="Security"
      icon="i-heroicons-shield-check"
    >
      <LazyUsersSettingsSecurity />
    </SettingsModalPage>

    <SettingsModalPage
      v-if="workspace && !workspace.is_readonly"
      id="connections"
      label="Connections"
      icon="i-heroicons-link"
    >
      <LazyUsersSettingsConnections />
    </SettingsModalPage>

    <SettingsModalPage
      v-if="workspace && !workspace.is_readonly"
      id="access-tokens"
      label="Access Tokens"
      icon="i-heroicons-key"
    >
      <LazyUsersSettingsAccessTokens />
    </SettingsModalPage>

    <SettingsModalPage
      v-if="user.has_customer_id"
      id="billing"
      label="Billing"
      icon="i-heroicons-credit-card"
    >
      <LazyUsersSettingsBilling />
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

const { current: workspace } = useCurrentWorkspace()
const { data: user } = useAuth().user()

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