import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { oauthApi } from '~/api/oauth'

export function useOAuth() {
  const queryClient = useQueryClient()

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

  // Mutations
  const connect = (options = {}) => {
    return useMutation({
      mutationFn: ({ service, data }) => oauthApi.connect(service, data),
      onSuccess: (newProvider) => {
        // Add to providers list
        queryClient.setQueryData(['oauth', 'providers'], (old) => {
          if (!old) return [newProvider]
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
      onSuccess: (updatedProvider) => {
        // Update provider in cache
        queryClient.setQueryData(['oauth', 'providers', updatedProvider.id], updatedProvider)
        
        // Update providers list
        queryClient.setQueryData(['oauth', 'providers'], (old) => {
          if (!old) return [updatedProvider]
          return old.map(provider => 
            provider.id === updatedProvider.id ? { ...provider, ...updatedProvider } : provider
          )
        })
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (providerId) => oauthApi.delete(providerId),
      onSuccess: (_, deletedProviderId) => {
        // Remove from individual cache
        queryClient.removeQueries(['oauth', 'providers', deletedProviderId])
        
        // Remove from providers list
        queryClient.setQueryData(['oauth', 'providers'], (old) => {
          if (!old) return old
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

  return {
    // Queries
    providers,
    provider,
    
    // Mutations
    connect,
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