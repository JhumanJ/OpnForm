import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { oauthApi } from '~/api/oauth'
import { WindowMessageTypes, useWindowMessage } from '~/composables/useWindowMessage'

export function useOAuth() {
  const queryClient = useQueryClient()
  const alert = useAlert()

  // Constants
  const googleDrivePermissionFileScope = 'https://www.googleapis.com/auth/drive.file'
  
  // Service definitions
  const services = computed(() => {
    return [
      {
        name: 'google',
        title: 'Google',
        icon: 'mdi:google',
        enabled: useFeatureFlag('services.google.auth', false),
        auth_type: 'redirect'
      },
      {
        name: 'stripe',
        title: 'Stripe',
        icon: 'cib:stripe',
        enabled: useFeatureFlag('billing.stripe_publishable_key', false),
        auth_type: 'redirect'
      },
      {
        name: 'telegram',
        title: 'Telegram',
        icon: 'mdi:telegram',
        enabled: useFeatureFlag('services.telegram.bot_id', false),
        auth_type: 'widget',
        widget_file: 'TelegramWidget'
      }
    ]
  })

  // Utility to get service configuration
  const getService = (service) => {
    return services.value.find((item) => item.name === service)
  }

  // Enhanced error handling for OAuth operations
  const handleOAuthError = (error) => {
    const message = error.response?.data?.message || error.data?.message
    alert.error(message ?? "An error occurred while connecting an account")
  }

  // Queries
  const providers = (options = {}) => {
    return useQuery({
      queryKey: ['oauth', 'providers'],
      queryFn: () => oauthApi.list(options),
      onSuccess: (data) => {
        data?.forEach(provider => {
          queryClient.setQueryData(['oauth', 'providers', provider.id], provider)
        })
      },
      ...options
    })
  }

  const provider = (providerId, options = {}) => {
    return useQuery({
      queryKey: ['oauth', 'providers', providerId],
      queryFn: () => {
        // Since there's no individual get endpoint, we get from the cached list
        const cachedProviders = queryClient.getQueryData(['oauth', 'providers'])
        return cachedProviders?.find(p => p.id === providerId) || null
      },
      enabled: !!providerId,
      ...options
    })
  }

  // Enhanced connect method with redirect/newtab/autoClose support
  const connect = (service, redirect = false, newtab = false, autoClose = false) => {
    const serviceConfig = getService(service)
    if (serviceConfig && serviceConfig.auth_type && serviceConfig.auth_type !== 'redirect') {
      return Promise.resolve()
    }

    const intention = redirect ? new URL(window.location.href).pathname : undefined
    
    return oauthApi.connect(service, {
      ...(intention && { intention }),
      autoClose: autoClose 
    })
      .then((data) => {
        if (newtab) {
          window.open(data.url, '_blank')
        } else {
          window.location.href = data.url
        }
      })
      .catch((error) => {
        handleOAuthError(error)
      })
  }

  // Guest connect method
  const guestConnect = (service, redirect = false) => {
    const intention = new URL(window.location.href).pathname

    return oauthApi.redirect(service, {
      ...redirect ? { intention } : {},
    })
      .then((data) => {
        window.open(data.url, '_blank')
      })
      .catch((error) => {
        handleOAuthError(error)
      })
  }

  // Mutation for connect (programmatic)
  const connectMutation = (options = {}) => {
    return useMutation({
      mutationFn: ({ service, data }) => oauthApi.connect(service, data),
      onSuccess: (newProvider) => {
      // Add to providers list
      queryClient.setQueryData(['oauth', 'providers'], (old) => {
        if (!old) return [newProvider]
        if (!Array.isArray(old)) return [newProvider]
        // Update if exists, add if new
        const existingIndex = old.findIndex(p => p.service === newProvider.service)
        if (existingIndex >= 0) {
          const updated = [...old]
          updated[existingIndex] = newProvider
          return updated
        }
        return [...old, newProvider]
      })
      // Cache individual provider
      queryClient.setQueryData(['oauth', 'providers', newProvider.id], newProvider)
      },
      ...options
    })
  }

  const callback = (options = {}) => {
    return useMutation({
      mutationFn: ({ service, data }) => oauthApi.callback(service, data),
      onSuccess: (updatedProvider) => {
      // Update provider in cache
      queryClient.setQueryData(['oauth', 'providers', updatedProvider.id], updatedProvider)
      
      // Update providers list
      queryClient.setQueryData(['oauth', 'providers'], (old) => {
        if (!old) return [updatedProvider]
        if (!Array.isArray(old)) return old
        return old.map(provider =>
          provider.id === updatedProvider.id ? { ...provider, ...updatedProvider } : provider
        )
      })
      },
      ...options
    })
  }

  const widgetCallback = (options = {}) => {
    return useMutation({
      mutationFn: ({ service, data }) => oauthApi.widgetCallback(service, data),
      onSuccess: (response) => {
       if (response.provider) {
          const updatedProvider = response.provider
          // Update provider in cache
          queryClient.setQueryData(['oauth', 'providers', updatedProvider.id], updatedProvider)
          
          // Update providers list
          queryClient.setQueryData(['oauth', 'providers'], (old) => {
            if (!old) return [updatedProvider]
            if (!Array.isArray(old)) return old
            const existingIndex = old.findIndex(provider => provider.id === updatedProvider.id)
            if (existingIndex >= 0) {
              // Update existing
              const updated = [...old]
              updated[existingIndex] = { ...old[existingIndex], ...updatedProvider }
              return updated
            }
            // Add as new if not found
            return [...old, updatedProvider]
          })
        }
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (providerId) => oauthApi.delete(providerId),
      onSuccess: (_, deletedProviderId) => {
      // Remove from individual cache
      queryClient.removeQueries({ queryKey: ['oauth', 'providers', deletedProviderId] } )
      
      // Remove from providers list
      queryClient.setQueryData(['oauth', 'providers'], (old) => {
        if (!Array.isArray(old)) return old
        return old.filter(provider => provider.id !== deletedProviderId)
      })
      },
      ...options
    })
  }

  const redirect = (options = {}) => {
    return useMutation({
      mutationFn: ({ provider, data }) => oauthApi.redirect(provider, data),
      // This mutation typically redirects to OAuth provider, so no cache update needed
      ...options
    })
  }

  // Window message integration for cache invalidation
  onMounted(() => {
    // Listen for OAuth provider connections to invalidate cache
    const windowMessage = useWindowMessage(WindowMessageTypes.OAUTH_PROVIDER_CONNECTED)
    
    windowMessage.listen((_event) => {
      // Invalidate providers cache when OAuth connection completes
      invalidateProviders()
    }, {
      useMessageChannel: false,
      acknowledge: false
    })
  })

  // Utility functions
  const prefetchProviders = () => {
    return queryClient.prefetchQuery({
      queryKey: ['oauth', 'providers'],
      queryFn: () => oauthApi.list()
    })
  }

  const invalidateProviders = () => {
    queryClient.invalidateQueries(['oauth', 'providers'])
  }

  const invalidateProvider = (providerId) => {
    queryClient.invalidateQueries(['oauth', 'providers', providerId])
  }

  const getProviderByService = (service) => {
    const providers = queryClient.getQueryData(['oauth', 'providers'])
    return providers?.find(p => p.service === service) || null
  }

  // Fetch providers method (wrapper for the query)
  const fetchOAuthProviders = () => {
    return queryClient.fetchQuery({
      queryKey: ['oauth', 'providers'],
      queryFn: () => oauthApi.list()
    })
  }

  return {
    // Constants
    googleDrivePermissionFileScope,
    
    // Service definitions
    services,
    getService,
    
    // Queries
    providers,
    provider,
    
    // Enhanced connect methods
    connect,
    guestConnect,
    fetchOAuthProviders,
    
    // Mutations
    connectMutation,
    callback,
    widgetCallback,
    remove,
    redirect,
    
    // Utilities
    prefetchProviders,
    invalidateProviders,
    invalidateProvider,
    getProviderByService
  }
} 