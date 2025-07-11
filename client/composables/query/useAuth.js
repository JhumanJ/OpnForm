import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { authApi } from '~/api/auth'
import { useAuthStore } from '~/stores/auth'
import { initServiceClients } from '~/composables/useAuthFlow'


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
        initServiceClients(userData)
      },
      ...options
    })
  }

  const updateProfile = (options = {}) => {
    return useMutation({
      mutationFn: (data) => authApi.user.updateProfile(data),
      onSuccess: (updatedUser) => {
        // Optimistically update user cache
        queryClient.setQueryData(['user'], (old) => {
          const newData = old ? { ...old, ...updatedUser } : updatedUser
          // Re-initialize service clients with potentially updated data
          initServiceClients(newData)
          return newData
        })
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
          initServiceClients(newData)
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
        authStore.clearToken()
        queryClient.clear()
      },
      ...options
    })
  }

  const login = (options = {}) => {
    return useMutation({
      mutationFn: (data) => authApi.login(data),
      ...options
    })
  }

  const register = (options = {}) => {
    return useMutation({
      mutationFn: (data) => authApi.register(data),
      ...options
    })
  }

  const logout = (options = {}) => {
    return useMutation({
      mutationFn: () => authApi.logout(),
      onSuccess: () => {
        // Clear auth state and all cached data
        authStore.clearToken()
        queryClient.clear()
      },
      onError: (error) => {
        console.error(error)
        // Even if logout API fails, clear local state
        authStore.clearToken()
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
        invalidateUser()
      },
      ...options
    })
  }

  const invalidateUser = () => {
    return queryClient.invalidateQueries(['user'])
  }

  return {
    // Queries
    user,
    
    // Mutations
    login,
    register,
    updateProfile,
    updateCredentials,
    deleteAccount,
    logout,
    oauthCallback,
    
    // Utilities
    invalidateUser
  }
} 