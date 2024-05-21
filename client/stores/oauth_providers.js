import { defineStore } from "pinia"
import { useContentStore } from "~/composables/stores/useContentStore.js"

export const providersEndpoint = "/open/providers"

export const useOAuthProvidersStore = defineStore("oauth_providers", () => {
  const contentStore = useContentStore()

  const fetchOAuthProviders = () => {
    contentStore.resetState()
    contentStore.startLoading()

    return opnFetch(providersEndpoint).then(
      (data) => {
        contentStore.save(data)
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
