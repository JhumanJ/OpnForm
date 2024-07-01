<template>
  <div
    v-if="customDomainsEnabled"
    id="custom-domains"
  >
    <UButton
      color="gray"
      label="Manage Custom Domains"
      icon="i-heroicons-globe-alt"
      @click="showCustomDomainModal = !showCustomDomainModal"
    />

    <modal
      :show="showCustomDomainModal"
      max-width="lg"
      @close="showCustomDomainModal = false"
    >
      <h4 class="mb-4 font-medium">
        Manage your custom domains
      </h4>
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
          </NuxtLink> to setup a custom domain.
        </template>
      </UAlert>
      <p class="text-gray-500 text-sm mb-4">
        Read
        <a
          href="#"
          class="underline"
          @click.prevent="
            crisp.openHelpdeskArticle('how-to-use-my-own-domain-9m77g7')
          "
        >our instructions</a>
        to learn how to setup your own domain.
      </p>
      <text-area-input
        :form="customDomainsForm"
        name="custom_domains"
        :required="false"
        :disabled="!workspace.is_pro"
        label="Workspace Custom Domains"
        wrapper-class=""
        placeholder="yourdomain.com - 1 per line"
      />
      <UButton
        class="mt-3"
        :loading="customDomainsLoading"
        :disabled="!workspace.is_pro"
        icon="i-heroicons-check"
        @click="saveChanges"
      >
        Save Domain(s)
      </UButton>
    </modal>
  </div>
</template>

<script setup>
import {watch} from "vue"

const crisp = useCrisp()
const workspacesStore = useWorkspacesStore()
const workspace = computed(() => workspacesStore.getCurrent)
const loading = computed(() => workspacesStore.loading)

const customDomainsForm = useForm({
  custom_domain: "",
})
const customDomainsLoading = ref(false)
const showCustomDomainModal = ref(false)

const customDomainsEnabled = computed(
  () => useRuntimeConfig().public.customDomainsEnabled,
)

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
  if (customDomainsLoading.value) return
  customDomainsLoading.value = true

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
      useAlert().success("Custom domains saved.")
    })
    .catch((error) => {
      useAlert().error(
        "Failed to update custom domains: " + error.response.data.message,
      )
    })
    .finally(() => {
      customDomainsLoading.value = false
    })
}

const initCustomDomains = () => {
  if (!workspace || !workspace.value.custom_domains) return
  customDomainsForm.custom_domains = workspace.value?.custom_domains.join("\n")
}
</script>
