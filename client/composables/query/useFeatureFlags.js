import { useQuery, useQueryClient } from '@tanstack/vue-query'
import { contentApi } from '~/api/content'

export function useFeatureFlags() {
  const queryClient = useQueryClient()

  // Main feature flags query
  const flags = (options = {}) => {
    return useQuery({
      queryKey: ['featureFlags'],
      queryFn: () => contentApi.featureFlags.list(),
      staleTime: 1000 * 60 * 60, // 1 hour,
      gcTime: 1000 * 60 * 60 * 24, // 24 hours
      refetchOnWindowFocus: false,
      ...options
    })
  }

  // Helper to get a specific flag value from cached query
  const getFlag = (path, defaultValue = false) => {
    const cachedData = queryClient.getQueryData(['featureFlags'])
    if (!cachedData) return defaultValue
    
    return path.split('.').reduce((acc, part) => {
      if (acc === undefined) return defaultValue
      return acc && acc[part] !== undefined ? acc[part] : defaultValue
    }, cachedData)
  }

  // Utility functions
  const invalidateFlags = () => {
    queryClient.invalidateQueries(['featureFlags'])
  }

  return {
    // Queries
    flags,
    
    // Utilities  
    getFlag,
    invalidateFlags
  }
} 