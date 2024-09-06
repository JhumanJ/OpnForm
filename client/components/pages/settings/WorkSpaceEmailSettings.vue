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
        Customize email sender with your own SMTP and send with your email.
      </p>
      <UAlert
        v-if="!workspace.is_pro"
        icon="i-heroicons-user-group-20-solid"
        class="mb-4"
        color="orange"
        variant="subtle"
        title="Pro plan required"
      >
        <template #description>
          Please <NuxtLink
            :to="{name:'pricing'}"
            class="underline"
          >
            upgrade your account
          </NuxtLink> to setup an email settings.
        </template>
      </UAlert>
      <p class="text-gray-500 text-sm mb-4">
        Read
        <a
          href="#"
          class="underline"
          @click.prevent="
            crisp.openHelpdesk
          "
        >our instructions</a>
        to learn how to setup email settings.
      </p>
      <TextInput
        :form="emailSettingsForm"
        name="host"
        :required="true"
        :disabled="!workspace.is_pro"
        label="Host/Server"
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
      <UButton
        class="mt-3"
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
    </modal>
  </div>
</template>

<script setup>
import {watch} from "vue"

const crisp = useCrisp()
const workspacesStore = useWorkspacesStore()
const workspace = computed(() => workspacesStore.getCurrent)

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
