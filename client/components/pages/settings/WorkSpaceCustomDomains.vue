<template>
  <div id="custom-domains">
    <UButton
      v-if="customDomainsEnabled"
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
      <template v-if="customDomainsEnabled">
        <p
          v-if="!workspace.is_pro"
          class="mb-4"
        >
          Please <NuxtLink :to="{name:'pricing'}">
            upgrade your account
          </NuxtLink> to setup a custom domain.
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
        <p class="text-gray-500 text-sm">
          Read our
          <a
            href="#"
            @click.prevent="
              crisp.openHelpdeskArticle('how-to-use-my-own-domain-9m77g7')
            "
          >custom domain instructions</a>
          to learn how to use your own domain.
        </p>
      </template>
      <UButton
        v-if="customDomainsEnabled"
        class="mt-3"
        :loading="customDomainsLoading"
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
