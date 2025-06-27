<template>
  <div class="space-y-8">
    <!-- Profile Information Section -->
    <div class="space-y-4">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Email Settings</h3>
        <p class="text-sm text-neutral-500 mt-1">Customize email sender - connect your SMTP server.</p>
      </div>

      <UAlert
        v-if="!workspace.is_pro"
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
          </NuxtLink> to setup an email settings.
        </template>
      </UAlert>

      <VForm size="sm">
        <form
          @submit.prevent="saveChanges"
          @keydown="emailSettingsForm.onKeydown($event)"
        >
          <div class="max-w-sm">
            <TextInput
              :form="emailSettingsForm"
              name="host"
              :required="true"
              :disabled="!workspace.is_pro"
              label="Host/Server"
              class="mt-2"
              placeholder="smtp.example.com"
            />
            <TextInput
              :form="emailSettingsForm"
              name="port"
              :required="true"
              :disabled="!workspace.is_pro"
              label="Port"
              placeholder="587"
            />
            <TextInput
              :form="emailSettingsForm"
              name="username"
              :required="true"
              :disabled="!workspace.is_pro"
              label="Username"
              placeholder="Username"
            />
            <TextInput
              :form="emailSettingsForm"
              name="password"
              native-type="password"
              :required="true"
              :disabled="!workspace.is_pro"
              label="Password"
              placeholder="Password"
            />
            <TextInput
              :form="emailSettingsForm"
              name="sender_address"
              :disabled="!workspace.is_pro"
              label="Sender address"
              placeholder="sender@example.com"
            />
          </div>

          <div class="mt-4">
            <UButton
              type="submit"
              :loading="emailSettingsForm.busy"
              :disabled="!workspace.is_pro"
              icon="i-heroicons-check"
            >
              Save Domain(s)
            </UButton>
            <UButton
              class="mt-3 ml-2"
              color="neutral"
              variant="outline"
              size="sm"
              :loading="emailSettingsForm.busy"
              :disabled="!workspace.is_pro"
              icon="i-heroicons-x-mark"
              @click="clearEmailSettings"
            >
              Clear
            </UButton>
          </div>
        </form>
      </VForm>
    </div>
  </div>
</template>

<script setup>
const workspacesStore = useWorkspacesStore()
const alert = useAlert()

const workspace = computed(() => workspacesStore.getCurrent)

const subscriptionModalStore = useSubscriptionModalStore()

const openSubscriptionModal = () => {
  subscriptionModalStore.setModalContent('Upgrade to send emails using your own domain')
  subscriptionModalStore.openModal()
}

const emailSettingsForm = useForm({
  host: '',
  port: '',
  username: '',
  password: '',
  sender_address: ''
})

onMounted(() => {
  initEmailSettings()
})

watch(
  () => workspace,
  () => {
    initEmailSettings()
  },
)

const clearEmailSettings = () => {
  emailSettingsForm.reset()
  saveChanges()
}

const saveChanges = () => {
  // Update the workspace Email Settings
  emailSettingsForm
    .put("/open/workspaces/" + workspace.value.id + "/email-settings", {
      data: {
        host: emailSettingsForm?.host,
        port: emailSettingsForm?.port,
        username: emailSettingsForm?.username,
        password: emailSettingsForm?.password,
        sender_address: emailSettingsForm?.sender_address,
      },
    })
    .then((data) => {
      workspacesStore.save(data)
      alert.success("Email settings saved.")
    })
    .catch((error) => {
      alert.error("Failed to update email settings: " + error.response.data.message)
    })
}

const initEmailSettings = () => {
  if (!workspace || !workspace.value.settings.email_settings) return
  const emailSettings = workspace.value?.settings?.email_settings
  emailSettingsForm.host = emailSettings?.host
  emailSettingsForm.port = emailSettings?.port
  emailSettingsForm.username = emailSettings?.username
  emailSettingsForm.password = emailSettings?.password
  emailSettingsForm.sender_address = emailSettings?.sender_address
}
</script> 