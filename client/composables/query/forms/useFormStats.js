import { useQueryClient, useQuery } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useFormStats() {
  const queryClient = useQueryClient()

  const stats = (workspaceId, formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats', options.params],
      queryFn: () => formsApi.stats(workspaceId, formId, options),
      enabled: !!(workspaceId && formId),
      ...options
    })
  }

  const statsDetails = (workspaceId, formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats-details'],
      queryFn: () => formsApi.statsDetails(workspaceId, formId, options),
      enabled: !!(workspaceId && formId),
      ...options
    })
  }

  // Utility functions
  const invalidateStats = (formId) => {
      queryClient.invalidateQueries({queryKey:['forms', formId, 'stats']})
      queryClient.invalidateQueries({queryKey:['forms', formId, 'stats-details']})
  }

  return {
    // Queries
    stats,
    statsDetails,
    
    // Utilities
    invalidateStats,
  }
} 