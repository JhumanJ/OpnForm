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
      <v-button
        v-if="customDomainsEnabled"
        class="w-full sm:w-auto mt-2"
        :loading="customDomainsLoading"
        @click="saveChanges"
      >
        <svg
          class="w-4 h-4 text-white inline mr-1 -mt-1"
          viewBox="0 0 24 24"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M17 21V13H7V21M7 3V8H15M19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V5C3 4.46957 3.21071 3.96086 3.58579 3.58579C3.96086 3.21071 4.46957 3 5 3H16L21 8V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21Z"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
        Save Domains
      </v-button>
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
