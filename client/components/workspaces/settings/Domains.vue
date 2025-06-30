<template>
  <div class="space-y-8">
    <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h3 class="text-lg font-medium text-neutral-900">Custom Domains Settings</h3>
        <p class="mt-1 text-sm text-neutral-500">
          Manage your custom domains.
        </p>
      </div>

      <div class="flex shrink-0 items-center gap-2">
        <UButton
          label="Help"
          icon="i-heroicons-question-mark-circle"
          variant="outline"
          color="primary"
          @click="crisp.openHelpdeskArticle('how-to-use-my-own-domain-9m77g7')"
        />
      </div>
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
        </NuxtLink> to setup a custom domain.
      </template>
    </UAlert>

    <VForm size="sm">
      <form
        @submit.prevent="saveChanges"
        @keydown="customDomainsForm.onKeydown($event)"
      >
        <div class="max-w-sm">
          <text-area-input
            :form="customDomainsForm"
            name="custom_domains"
            :required="false"
            :disabled="!workspace.is_pro"
            label="Workspace Custom Domains"
            wrapper-class=""
            placeholder="yourdomain.com - 1 per line"
          />
        </div>

        <div class="mt-4">
          <UButton
            type="submit"
            :loading="customDomainsForm.busy"
            :disabled="!workspace.is_pro"
            icon="i-heroicons-check"
          >
            Save Domain(s)
          </UButton>
        </div>
      </form>
    </VForm>
  </div>
</template>

<script setup>
const workspacesStore = useWorkspacesStore()
const alert = useAlert()
const crisp = useCrisp()

const workspace = computed(() => workspacesStore.getCurrent)

const subscriptionModalStore = useSubscriptionModalStore()

const openSubscriptionModal = () => {
  subscriptionModalStore.setModalContent('Upgrade to setup custom domains')
  subscriptionModalStore.openModal()
}

const customDomainsForm = useForm({
  custom_domain: '',
})

onMounted(() => {
  initCustomDomains()
})

watch(
  () => workspace,
  () => {
    initCustomDomains()
  },
)

const saveChanges = () => {
  // Update the workspace custom domain
  customDomainsForm
    .put("/open/workspaces/" + workspace.value.id + "/custom-domains", {
      data: {
        custom_domains: customDomainsForm?.custom_domains
          ?.split("\n")
          .map((domain) => (domain ? domain.trim() : null))
          .filter((domain) => domain && domain.length > 0),
      },
    })
    .then((data) => {
      workspacesStore.save(data)
      alert.success("Custom domains saved.")
    })
    .catch((error) => {
      alert.error(error.response._data.message ?? 'Failed to update custom domains')
    })
}

const initCustomDomains = () => {
  if (!workspace || !workspace.value.custom_domains) return
  customDomainsForm.custom_domains = workspace.value?.custom_domains.join("\n")
}
</script> 