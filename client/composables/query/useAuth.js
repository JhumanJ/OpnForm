import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { authApi } from '~/api/auth'
import { useAuthStore } from '~/stores/auth'

export function useAuth() {
  const queryClient = useQueryClient()
  const authStore = useAuthStore()

  // Queries
  const user = (options = {}) => {
    return useQuery({
      queryKey: ['user'],
      queryFn: () => authApi.user.get(options),
      enabled: !!authStore.token,
      staleTime: 5 * 60 * 1000, // 5 minutes
      onSuccess: (userData) => {
        // Coordinate with auth store for service client initialization
        authStore.initServiceClients(userData)
      },
      onError: (error) => {
        // If user fetch fails due to invalid token, clear auth state
        if (error.status === 401) {
          authStore.clearTokens()
          queryClient.clear()
        }
      },
      ...options
    })
  }

  // Mutations
  const updateCredentials = (options = {}) => {
    return useMutation({
      mutationFn: (data) => authApi.user.updateCredentials(data),
      onSuccess: (updatedUser) => {
        // Update user cache with optimistic updates
        queryClient.setQueryData(['user'], (old) => {
          const newData = old ? { ...old, ...updatedUser } : updatedUser
          // Re-initialize service clients with updated data
          authStore.initServiceClients(newData)
          return newData
        })
      },
      ...options
    })
  }

  const deleteAccount = (options = {}) => {
    return useMutation({
      mutationFn: () => authApi.user.delete(),
      onSuccess: () => {
        // Clear auth state and all cached data
        authStore.clearTokens()
        queryClient.clear()
      },
      ...options
    })
  }

  const logout = (options = {}) => {
    return useMutation({
      mutationFn: () => authApi.logout(),
      onSuccess: () => {
        // Clear auth state and all cached data
        authStore.clearTokens()
        queryClient.clear()
      },
      onError: () => {
        // Even if logout API fails, clear local state
        authStore.clearTokens()
        queryClient.clear()
      },
      ...options
    })
  }

  const oauthCallback = (options = {}) => {
    return useMutation({
      mutationFn: ({ provider, data }) => authApi.oauth.callback(provider, data),
      onSuccess: (response) => {
        // Handle token from OAuth callback
        if (response.token) {
          authStore.setToken(response.token, response.expires_in)
        }
        
        // Invalidate user cache to trigger fresh fetch with new token
        queryClient.invalidateQueries(['user'])
      },
      ...options
    })
  }

  // Utility functions
  const prefetchUser = () => {
    if (!authStore.token) return Promise.resolve()
    
    return queryClient.prefetchQuery({
      queryKey: ['user'],
      queryFn: () => authApi.user.get(),
      staleTime: 15 * 60 * 1000
    })
  }

  const invalidateUser = () => {
    return queryClient.invalidateQueries(['user'])
  }

  return {
    // Queries
    user,
    
    // Mutations
    updateCredentials,
    deleteAccount,
    logout,
    oauthCallback,
    
    // Utilities
    prefetchUser,
    invalidateUser
  }
} 