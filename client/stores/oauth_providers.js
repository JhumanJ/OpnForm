import { defineStore } from "pinia"
import { useContentStore } from "~/composables/stores/useContentStore.js"

export const providersEndpoint = "/open/providers"

export const useOAuthProvidersStore = defineStore("oauth_providers", () => {
  const contentStore = useContentStore()
  const alert = useAlert()

  const googleDrivePermission = 'https://www.googleapis.com/auth/drive.file'

  const services = computed(() => {
    return [
      {
        name: 'google',
        title: 'Google',
        icon: 'mdi:google',
        enabled: true
      }
    ]
  })

  const getService = (service) => {
    return services.value.find((item) => item.name === service)
  }

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

  const connect = (service, redirect = false) => {
    contentStore.resetState()
    contentStore.startLoading()

    const intention = new URL(window.location.href).pathname

    opnFetch(`/settings/providers/connect/${service}`, {
      method: 'POST',
      body: {
        ...redirect ? { intention } : {},
      }
    })
      .then((data) => {
        window.location.href = data.url
      })
      .catch((error) => {
        try {
          alert.error(error.data.message)
        } catch (e) {
          alert.error("An error occurred while connecting an account")
        }
      })
      .finally(() => {
        contentStore.stopLoading()
      })
  }

  const guestConnect = (service, redirect = false) => {
    contentStore.resetState()
    contentStore.startLoading()

    const intention = new URL(window.location.href).pathname

    opnFetch(`/oauth/connect/${service}`, {
      method: 'POST',
      body: {
        ...redirect ? { intention } : {},
      }
    })
      .then((data) => {
        window.open(data.url, '_blank')
      })
      .catch((error) => {
        try {
          alert.error(error.data.message)
        } catch (e) {
          alert.error("An error occurred while connecting an account")
        }
      })
      .finally(() => {
        contentStore.stopLoading()
      })
  }

  const providers = computed(() => contentStore.getAll.value)

  return {
    ...contentStore,
    googleDrivePermission,
    services,
    getService,
    fetchOAuthProviders,
    providers,
    connect,
    guestConnect
  }
})
