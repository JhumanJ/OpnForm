import { useQueryClient, useQuery } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useFormStats() {
  const queryClient = useQueryClient()

  const stats = (workspaceId, formId, fromDate, toDate, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats', fromDate, toDate],
      queryFn: () => formsApi.stats(workspaceId, formId, {...options, params: {date_from: fromDate.value, date_to: toDate.value}}),
      ...options
    })
  }

  const statsDetails = (workspaceId, formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats-details'],
      queryFn: () => formsApi.statsDetails(workspaceId, formId, options),
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