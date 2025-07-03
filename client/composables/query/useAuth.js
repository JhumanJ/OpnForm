import { authApi } from '~/api/auth'

export function useAuth() {
  const queryClient = useQueryClient()

  // Queries
  const user = (options = {}) => {
    return useQuery({
      queryKey: ['user'],
      queryFn: () => authApi.user.get(options),
      staleTime: 5 * 60 * 1000,
      ...options
    })
  }

  // Mutations
  const updateCredentials = (options = {}) => {
    return useMutation({
      mutationFn: (data) => authApi.user.updateCredentials(data),
      onSuccess: (updatedUser) => {
        // Update user cache
        queryClient.setQueryData(['user'], (old) => {
          return old ? { ...old, ...updatedUser } : updatedUser
        })
      },
      ...options
    })
  }

  const deleteAccount = (options = {}) => {
    return useMutation({
      mutationFn: () => authApi.user.delete(),
      onSuccess: () => {
        // Clear all user-related data
        queryClient.clear()
      },
      ...options
    })
  }

  const logout = (options = {}) => {
    return useMutation({
      mutationFn: () => authApi.logout(),
      onSuccess: () => {
        // Clear all cached data on logout
        queryClient.clear()
      },
      ...options
    })
  }

  const oauthCallback = (options = {}) => {
    return useMutation({
      mutationFn: ({ provider, data }) => authApi.oauth.callback(provider, data),
      onSuccess: (userData) => {
        // Update user cache with OAuth data
        queryClient.setQueryData(['user'], (old) => {
          return old ? { ...old, ...userData } : userData
        })
      },
      ...options
    })
  }

  // Utility functions
  const prefetchUser = () => {
    return queryClient.prefetchQuery({
      queryKey: ['user'],
      queryFn: () => authApi.user.get(),
      staleTime: 5 * 60 * 1000
    })
  }

  const invalidateUser = () => {
    queryClient.invalidateQueries(['user'])
  }

  const clearUserCache = () => {
    queryClient.removeQueries(['user'])
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
    invalidateUser,
    clearUserCache
  }
} 