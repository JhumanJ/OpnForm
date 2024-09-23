<template>
  <div id="email-settings">
    <UButton
      color="gray"
      label="Email Settings"
      icon="i-heroicons-envelope"
      @click="showEmailSettingsModal = !showEmailSettingsModal"
    />

    <modal
      :show="showEmailSettingsModal"
      max-width="lg"
      @close="showEmailSettingsModal = false"
    >
      <h4 class="font-medium">
        Email Settings
      </h4>
      <p class="mb-4 text-gray-500 text-sm">
        Customize email sender - connect your SMTP server.
      </p>
      <UAlert
        v-if="!workspace.is_pro"
        icon="i-heroicons-user-group-20-solid"
        class="my-4 !text-orange-500"
        color="orange"
        variant="subtle"
        title="Pro plan required"
      >
        <template #description>
          Please <a
            href="#"
            class="text-orange-500 underline"
            @click.prevent="openSubscriptionModal"
          >
            upgrade your account
          </a> to setup an email settings.
        </template>
      </UAlert>
       
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

      <div class="flex justify-between gap-2">
        <UButton
          class="mt-3 px-6"
          :loading="emailSettingsLoading"
          :disabled="!workspace.is_pro"
          icon="i-heroicons-check"
          @click="saveChanges"
        >
          Update
        </UButton>
        <UButton
          class="mt-3 ml-2"
          color="white"
          size="sm"
          :loading="emailSettingsLoading"
          :disabled="!workspace.is_pro"
          icon="i-heroicons-x-mark"
          @click="clearEmailSettings"
        >
          Clear
        </UButton>
      </div>
    </modal>
  </div>
</template>

<script setup>
import {watch} from "vue"

const workspacesStore = useWorkspacesStore()
const workspace = computed(() => workspacesStore.getCurrent)
const subscriptionModalStore = useSubscriptionModalStore()

const openSubscriptionModal = () => {
  showEmailSettingsModal.value = false
  subscriptionModalStore.setModalContent('Upgrade to send emails using your own domain')
  subscriptionModalStore.openModal()
}

const emailSettingsForm = useForm({
  host: '',
  port: '',
  username: '',
  password: ''
})
const emailSettingsLoading = ref(false)
const showEmailSettingsModal = ref(false)

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
  if (emailSettingsLoading.value) return
  emailSettingsLoading.value = true

  // Update the workspace Email Settings
  emailSettingsForm
    .put("/open/workspaces/" + workspace.value.id + "/email-settings", {
      data: {
        host: emailSettingsForm?.host,
        port: emailSettingsForm?.port,
        username: emailSettingsForm?.username,
        password: emailSettingsForm?.password,
      },
    })
    .then((data) => {
      workspacesStore.save(data)
      useAlert().success("Email settings saved.")
    })
    .catch((error) => {
      useAlert().error(
        "Failed to update email settings: " + error.response.data.message,
      )
    })
    .finally(() => {
      emailSettingsLoading.value = false
    })
}

const initEmailSettings = () => {
  if (!workspace || !workspace.value.settings.email_settings) return
  const emailSettings = workspace.value?.settings?.email_settings
  emailSettingsForm.host = emailSettings?.host
  emailSettingsForm.port = emailSettings?.port
  emailSettingsForm.username = emailSettings?.username
  emailSettingsForm.password = emailSettings?.password
}
</script>
