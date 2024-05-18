import { defineStore } from "pinia"
import { useContentStore } from "~/composables/stores/useContentStore.js"

export const providersEndpoint = "/open/workspaces/{workspaceid}/providers"

export const useOAuthProvidersStore = defineStore("oauth_providers", () => {
  const contentStore = useContentStore()

  const fetchOAuthProviders = (workspaceId) => {
    contentStore.resetState()
    contentStore.startLoading()
    return useOpnApi(providersEndpoint.replace('{workspaceid}', workspaceId)).then(
      (response) => {
        contentStore.save(response.data.value)
        contentStore.stopLoading()
      },
    )
  }

  const providers = computed(() => contentStore.getAll.value)

  return {
    ...contentStore,
    fetchOAuthProviders,
    providers
  }
})
