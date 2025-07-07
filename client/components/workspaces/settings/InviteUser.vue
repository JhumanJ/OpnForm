<template>
  <UModal
    v-model:open="isOpen"
    :content="{
      onPointerDownOutside: (event) => { if (event.target?.closest('.crisp-client')) {return event.preventDefault()}}
    }"
  >
    <template #header>
      <div class="flex items-center w-full gap-4 px-2">
        <h2 class="font-semibold">
          Invite a new user
        </h2>
      </div>
      <UButton
        color="neutral"
        variant="outline"
        icon="i-heroicons-question-mark-circle"
        size="sm"
        @click="crisp.openHelpdeskArticle('how-to-invite-users-team-members-to-my-workspace-qyw16g')"
      >
        Help
      </UButton>
    </template>

    <template #body>
      <template v-if="paidPlansEnabled && !hasActiveLicense">
        <UAlert
          v-if="workspace.is_pro"
          icon="i-heroicons-credit-card"
          color="primary"
          variant="subtle"
          title="This is a billable event."
        >
          <template #description>
            You will be charged $6/month for each user you invite to this workspace. More details on the
            <NuxtLink
              target="_blank"
              class="underline cursor-pointer"
              @click="openBilling"
            >
              billing
            </NuxtLink>
            and
            <NuxtLink
              target="_blank"
              class="underline"
              :to="{name:'pricing'}"
            >
              pricing
            </NuxtLink>
            page.
          </template>
        </UAlert>
        <UAlert
          v-else
          icon="i-heroicons-user-group-20-solid"
          class="mb-4"
          color="warning"
          variant="subtle"
          title="Pro plan required"
        >
          <template #description>
            Please <NuxtLink
              @click.prevent="openSubscriptionModal"
              class="underline"
            >
              upgrade your account
            </NuxtLink> to invite users to your workspace.
          </template>
        </UAlert>
      </template>

      <form
        class="my-2"
        @submit.prevent="addUser"
        @keydown="inviteUserForm.onKeydown($event)"
      >
        <TextInput
          :form="inviteUserForm"
          name="email"
          label="Email"
          :required="true"
          :disabled="!workspace.is_pro"
          placeholder="Add a new user by email"
        />
        <SelectInput
          :form="inviteUserForm"
          name="role"
          :options="roleOptions"
          :disabled="!workspace.is_pro"
          placeholder="Select User Role"
          label="Role"
          :required="true"
        />
        <div class="flex justify-center mt-4">
          <UButton
            type="submit"
            :disabled="!workspace.is_pro"
            :loading="inviteUserMutation.isPending.value"
            icon="i-heroicons-envelope"
          >
            Invite User
          </UButton>
        </div>
      </form>
    </template>
  </UModal>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false
  }
})

const { hasActiveLicense } = useAuthFlow()
const { addUser: addUserMutation } = useWorkspaceUsers()
const crisp = useCrisp()
const subscriptionModalStore = useSubscriptionModalStore()
const { current: workspace, currentId: workspaceId } = useCurrentWorkspace()
const alert = useAlert()

const emit = defineEmits(['update:modelValue', 'user-added'])

const roleOptions = [
  {name: "User", value: "user"},
  {name: "Admin", value: "admin"},
  {name: "Read Only", value: "readonly"}
]

// Modal state
const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Create mutation during setup
const inviteUserMutation = addUserMutation(workspaceId, {
  onSuccess: (data) => {
    inviteUserForm.reset()
    alert.success(data.message || 'User invited successfully')
    emit('user-added')
    closeModal()
  },
  onError: (error) => {
    alert.error(error.response?.data?.message || "There was an error adding user")
  }
})

// Methods
const closeModal = () => {
  isOpen.value = false
}

const openSubscriptionModal = () => {
  subscriptionModalStore.setModalContent('Upgrade to invite users to your workspace')
  subscriptionModalStore.openModal()
}

const paidPlansEnabled = ref(useFeatureFlag('billing.enabled'))

const inviteUserForm = useForm({
  email: '',
  role: 'user'
})

const openBilling = () => {
  closeModal()
  useAppModals().openUserSettings('billing')
}

const addUser = () => {
  if (!workspaceId.value) return

  inviteUserMutation.mutate({
    email: inviteUserForm.email,
    role: inviteUserForm.role
  })
}
</script>
