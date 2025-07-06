import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'
import integrationsList from '~/data/forms/integrations.json'

export function useFormIntegrations() {
  const queryClient = useQueryClient()

  // Static integrations data
  const integrations = ref(new Map())

  // Initialize integrations from JSON
  const initIntegrations = () => {
    if (integrations.value.size === 0) {
      integrations.value = new Map(Object.entries(integrationsList))
    }
  }

  // Computed property for available integrations based on user subscription and feature flags
  const availableIntegrations = computed(() => {
    const user = useAuthStore().user
    const featureFlagsStore = useFeatureFlagsStore()
    if (!user) return integrations.value

    const enrichedIntegrations = new Map()
    for (const [key, integration] of integrations.value.entries()) {
      if (featureFlagsStore.getFlag(`integrations.${key}`, true)) {
        enrichedIntegrations.set(key, {
          ...integration,
          id: key,
          requires_subscription: !user.is_subscribed && integration.is_pro,
        })
      }
    }

    return enrichedIntegrations
  })

  // Computed property for integrations grouped by section
  const integrationsBySection = computed(() => {
    const groupedObject = {}
    for (const [key, integration] of availableIntegrations.value.entries()) {
      const sectionName = integration.section_name
      if (!groupedObject[sectionName]) {
        groupedObject[sectionName] = {}
      }
      groupedObject[sectionName][key] = integration
    }
    return groupedObject
  })

  // TanStack Query functions
  const list = (formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'integrations'],
      queryFn: () => formsApi.integrations.list(formId, options),
      enabled: !!formId,
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

  // Utility function to get all integrations by form ID from cache
  const getAllByFormId = (formId) => {
    const queryData = queryClient.getQueryData(['forms', formId, 'integrations'])
    return queryData || []
  }

  // Initialize integrations on first use
  initIntegrations()

  return {
    // Static data
    integrations: readonly(integrations),
    availableIntegrations,
    integrationsBySection,
    
    // TanStack Query functions
    list,
    integrationEvents,
    createIntegration,
    updateIntegration,
    deleteIntegration,
    invalidateIntegrations,
    
    // Utility functions
    getAllByFormId,
    initIntegrations,
  }
} 