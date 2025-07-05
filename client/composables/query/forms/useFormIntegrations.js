import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useFormIntegrations() {
  const queryClient = useQueryClient()

  const integrations = (formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'integrations'],
      queryFn: () => formsApi.integrations.list(formId, options),
      enabled: !!formId,
      staleTime: 5 * 60 * 1000,
      onSuccess: (data) => {
        data?.forEach(integration => {
          queryClient.setQueryData(['integrations', integration.id], integration)
        })
      },
      ...options
    })
  }

  const integrationEvents = (formId, integrationId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'integrations', integrationId, 'events'],
      queryFn: () => formsApi.integrations.events(formId, integrationId, options),
      enabled: !!(formId && integrationId),
      staleTime: 2 * 60 * 1000,
      ...options
    })
  }

  const createIntegration = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, data }) => formsApi.integrations.create(formId, data),
      onSuccess: (newIntegration, { formId }) => {
        // Add to integrations list
        queryClient.setQueriesData(['forms', formId, 'integrations'], (old) => {
          if (!old) return [newIntegration]
          return [...old, newIntegration]
        })
        // Cache the integration
        queryClient.setQueryData(['integrations', newIntegration.id], newIntegration)
      },
      ...options
    })
  }

  const updateIntegration = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, integrationId, data }) => formsApi.integrations.update(formId, integrationId, data),
      onSuccess: (updatedIntegration, { formId, integrationId }) => {
        // Update integration cache
        queryClient.setQueryData(['integrations', integrationId], updatedIntegration)
        
        // Update in integrations list
        queryClient.setQueriesData(['forms', formId, 'integrations'], (old) => {
          if (!old) return old
          return old.map(integration => 
            integration.id === integrationId ? { ...integration, ...updatedIntegration } : integration
          )
        })
      },
      ...options
    })
  }

  const deleteIntegration = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, integrationId }) => formsApi.integrations.delete(formId, integrationId),
      onSuccess: (_, { formId, integrationId }) => {
        // Remove from integration cache
        queryClient.removeQueries(['integrations', integrationId])
        queryClient.removeQueries(['forms', formId, 'integrations', integrationId, 'events'])
        
        // Remove from integrations list
        queryClient.setQueriesData(['forms', formId, 'integrations'], (old) => {
          if (!old) return old
          return old.filter(integration => integration.id !== integrationId)
        })
      },
      ...options
    })
  }

  const invalidateIntegrations = (formId) => {
    queryClient.invalidateQueries(['forms', formId, 'integrations'])
  }

  return {
    integrations,
    integrationEvents,
    createIntegration,
    updateIntegration,
    deleteIntegration,
    invalidateIntegrations,
  }
} 