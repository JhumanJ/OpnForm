import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useForms() {
  const queryClient = useQueryClient()

  // Form Queries
  const list = (workspaceId, options = {}) => {
    console.log('list', workspaceId, options)
    return useQuery({
      queryKey: ['forms', 'list', workspaceId, options.filters],
      queryFn: () => formsApi.list(workspaceId, options),
      enabled: !!workspaceId,
      staleTime: 5 * 60 * 1000,
      onSuccess: (data) => {
        data?.forEach(form => {
          queryClient.setQueryData(['forms', form.id], form)
          queryClient.setQueryData(['forms', 'slug', form.slug], form)
        })
      },
      ...options
    })
  }

  const detail = (slug, options = {}) => {
    return useQuery({
      queryKey: ['forms', 'slug', slug],
      queryFn: () => formsApi.get(slug, options),
      enabled: !!slug,
      staleTime: 5 * 60 * 1000,
      onSuccess: (form) => {
        if (form) {
          queryClient.setQueryData(['forms', form.id], form)
        }
      },
      ...options
    })
  }

  const detailById = (id, options = {}) => {
    return useQuery({
      queryKey: ['forms', id],
      queryFn: () => formsApi.getById(id, options),
      enabled: !!id,
      staleTime: 5 * 60 * 1000,
      onSuccess: (form) => {
        if (form) {
          queryClient.setQueryData(['forms', 'slug', form.slug], form)
        }
      },
      ...options
    })
  }

  // Paginated list
  const paginatedList = (workspaceId, page = ref(1), filters = ref({}), options = {}) => {
    return useQuery({
      queryKey: ['forms', 'list', workspaceId, { page: page.value, ...filters.value }],
      queryFn: () => formsApi.list(workspaceId, { page: page.value, ...filters.value }),
      enabled: !!workspaceId,
      keepPreviousData: true,
      staleTime: 5 * 60 * 1000,
      onSuccess: (data) => {
        data?.data?.forEach(form => {
          queryClient.setQueryData(['forms', form.id], form)
          queryClient.setQueryData(['forms', 'slug', form.slug], form)
        })
      },
      ...options
    })
  }

  // Submissions Queries
  const submissions = (formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'submissions', options.filters],
      queryFn: () => formsApi.submissions.list(formId, options),
      enabled: !!formId,
      staleTime: 2 * 60 * 1000,
      onSuccess: (data) => {
        data?.forEach(submission => {
          queryClient.setQueryData(['submissions', submission.id], submission)
        })
      },
      ...options
    })
  }

  const paginatedSubmissions = (formId, page = ref(1), filters = ref({}), options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'submissions', { page: page.value, ...filters.value }],
      queryFn: () => formsApi.submissions.list(formId, { page: page.value, ...filters.value }),
      enabled: !!formId,
      keepPreviousData: true,
      staleTime: 2 * 60 * 1000,
      onSuccess: (data) => {
        data?.data?.forEach(submission => {
          queryClient.setQueryData(['submissions', submission.id], submission)
        })
      },
      ...options
    })
  }

  const submissionDetail = (slug, submissionId, options = {}) => {
    return useQuery({
      queryKey: ['submissions', submissionId],
      queryFn: () => formsApi.submissions.get(slug, submissionId, options),
      enabled: !!(slug && submissionId),
      staleTime: 2 * 60 * 1000,
      ...options
    })
  }

  // Stats Queries
  const stats = (workspaceId, formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats'],
      queryFn: () => formsApi.stats(workspaceId, formId, options),
      enabled: !!(workspaceId && formId),
      staleTime: 5 * 60 * 1000,
      ...options
    })
  }

  const statsDetails = (workspaceId, formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats-details'],
      queryFn: () => formsApi.statsDetails(workspaceId, formId, options),
      enabled: !!(workspaceId && formId),
      staleTime: 5 * 60 * 1000,
      ...options
    })
  }

  // Integrations Queries
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

  // AI Generation Queries
  const aiGeneration = (generationId, options = {}) => {
    return useQuery({
      queryKey: ['ai', 'generation', generationId],
      queryFn: () => formsApi.ai.get(generationId, options),
      enabled: !!generationId,
      staleTime: 1 * 60 * 1000,
      refetchInterval: (data) => {
        // Poll if generation is in progress
        return data?.status === 'processing' ? 2000 : false
      },
      ...options
    })
  }

  // Form Mutations
  const create = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.create(data),
      onSuccess: (newForm) => {
        // Update workspace forms list
        queryClient.setQueriesData(['forms', 'list', newForm.workspace_id], (old) => {
          if (!old) return [newForm]
          if (Array.isArray(old)) return [newForm, ...old]
          if (old.data) {
            return {
              ...old,
              data: [newForm, ...old.data]
            }
          }
          return old
        })
        // Cache the new form
        queryClient.setQueryData(['forms', newForm.id], newForm)
        queryClient.setQueryData(['forms', 'slug', newForm.slug], newForm)
      },
      ...options
    })
  }

  const update = (options = {}) => {
    return useMutation({
      mutationFn: ({ id, data }) => formsApi.update(id, data),
      onSuccess: (updatedForm, { id }) => {
        // Update individual form cache
        queryClient.setQueryData(['forms', id], updatedForm)
        if (updatedForm.slug) {
          queryClient.setQueryData(['forms', 'slug', updatedForm.slug], updatedForm)
        }
        
        // Update in workspace lists
        queryClient.setQueriesData(['forms', 'list'], (old) => {
          if (!old) return old
          if (Array.isArray(old)) {
            return old.map(form => 
              form.id === id ? { ...form, ...updatedForm } : form
            )
          }
          if (old.data) {
            return {
              ...old,
              data: old.data.map(form => 
                form.id === id ? { ...form, ...updatedForm } : form
              )
            }
          }
          return old
        })
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (id) => formsApi.delete(id),
      onSuccess: (_, deletedId) => {
        const deletedForm = queryClient.getQueryData(['forms', deletedId])
        
        // Remove from all caches
        queryClient.removeQueries(['forms', deletedId])
        if (deletedForm?.slug) {
          queryClient.removeQueries(['forms', 'slug', deletedForm.slug])
        }
        queryClient.removeQueries(['forms', deletedId, 'submissions'])
        queryClient.removeQueries(['forms', deletedId, 'stats'])
        
        // Remove from workspace lists
        queryClient.setQueriesData(['forms', 'list'], (old) => {
          if (!old) return old
          if (Array.isArray(old)) {
            return old.filter(form => form.id !== deletedId)
          }
          if (old.data) {
            return {
              ...old,
              data: old.data.filter(form => form.id !== deletedId)
            }
          }
          return old
        })
      },
      ...options
    })
  }

  const duplicate = (options = {}) => {
    return useMutation({
      mutationFn: (id) => formsApi.duplicate(id),
      onSuccess: (duplicatedForm) => {
        // Add to workspace forms list
        queryClient.setQueriesData(['forms', 'list', duplicatedForm.workspace_id], (old) => {
          if (!old) return [duplicatedForm]
          if (Array.isArray(old)) return [duplicatedForm, ...old]
          if (old.data) {
            return {
              ...old,
              data: [duplicatedForm, ...old.data]
            }
          }
          return old
        })
        // Cache the duplicated form
        queryClient.setQueryData(['forms', duplicatedForm.id], duplicatedForm)
        queryClient.setQueryData(['forms', 'slug', duplicatedForm.slug], duplicatedForm)
      },
      ...options
    })
  }

  const regenerateLink = (options = {}) => {
    return useMutation({
      mutationFn: ({ id, option }) => formsApi.regenerateLink(id, option),
      onSuccess: (updatedForm, { id }) => {
        queryClient.setQueryData(['forms', id], (old) => {
          return old ? { ...old, ...updatedForm } : updatedForm
        })
        if (updatedForm.slug) {
          queryClient.setQueryData(['forms', 'slug', updatedForm.slug], (old) => {
            return old ? { ...old, ...updatedForm } : updatedForm
          })
        }
      },
      ...options
    })
  }

  const updateWorkspace = (options = {}) => {
    return useMutation({
      mutationFn: ({ id, workspaceId, data }) => formsApi.updateWorkspace(id, workspaceId, data),
      onSuccess: (updatedForm, { id, workspaceId: newWorkspaceId }) => {
        const oldForm = queryClient.getQueryData(['forms', id])
        const oldWorkspaceId = oldForm?.workspace_id
        
        // Update form cache
        queryClient.setQueryData(['forms', id], updatedForm)
        if (updatedForm.slug) {
          queryClient.setQueryData(['forms', 'slug', updatedForm.slug], updatedForm)
        }
        
        // Remove from old workspace list
        if (oldWorkspaceId) {
          queryClient.setQueriesData(['forms', 'list', oldWorkspaceId], (old) => {
            if (!old) return old
            if (Array.isArray(old)) {
              return old.filter(form => form.id !== id)
            }
            if (old.data) {
              return {
                ...old,
                data: old.data.filter(form => form.id !== id)
              }
            }
            return old
          })
        }
        
        // Add to new workspace list
        queryClient.setQueriesData(['forms', 'list', newWorkspaceId], (old) => {
          if (!old) return [updatedForm]
          if (Array.isArray(old)) return [updatedForm, ...old]
          if (old.data) {
            return {
              ...old,
              data: [updatedForm, ...old.data]
            }
          }
          return old
        })
      },
      ...options
    })
  }

  // Submission Mutations
  const submitForm = (options = {}) => {
    return useMutation({
      mutationFn: ({ slug, data, submitOptions }) => formsApi.submissions.answer(slug, data, submitOptions),
      onSuccess: (newSubmission, { slug }) => {
        // Get form by slug to find formId
        const form = queryClient.getQueryData(['forms', 'slug', slug])
        if (form) {
          // Add to submissions list
          queryClient.setQueriesData(['forms', form.id, 'submissions'], (old) => {
            if (!old) return [newSubmission]
            if (Array.isArray(old)) return [newSubmission, ...old]
            if (old.data) {
              return {
                ...old,
                data: [newSubmission, ...old.data]
              }
            }
            return old
          })
          // Cache the submission
          queryClient.setQueryData(['submissions', newSubmission.id], newSubmission)
          // Invalidate stats
          queryClient.invalidateQueries(['forms', form.id, 'stats'])
        }
      },
      ...options
    })
  }

  const updateSubmission = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, submissionId, data }) => formsApi.submissions.update(formId, submissionId, data),
      onSuccess: (updatedSubmission, { formId, submissionId }) => {
        // Update submission cache
        queryClient.setQueryData(['submissions', submissionId], updatedSubmission)
        
        // Update in submissions list
        queryClient.setQueriesData(['forms', formId, 'submissions'], (old) => {
          if (!old) return old
          if (Array.isArray(old)) {
            return old.map(submission => 
              submission.id === submissionId ? { ...submission, ...updatedSubmission } : submission
            )
          }
          if (old.data) {
            return {
              ...old,
              data: old.data.map(submission => 
                submission.id === submissionId ? { ...submission, ...updatedSubmission } : submission
              )
            }
          }
          return old
        })
      },
      ...options
    })
  }

  const deleteSubmission = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, submissionId }) => formsApi.submissions.delete(formId, submissionId),
      onSuccess: (_, { formId, submissionId }) => {
        // Remove from submission cache
        queryClient.removeQueries(['submissions', submissionId])
        
        // Remove from submissions list
        queryClient.setQueriesData(['forms', formId, 'submissions'], (old) => {
          if (!old) return old
          if (Array.isArray(old)) {
            return old.filter(submission => submission.id !== submissionId)
          }
          if (old.data) {
            return {
              ...old,
              data: old.data.filter(submission => submission.id !== submissionId)
            }
          }
          return old
        })
        
        // Invalidate stats
        queryClient.invalidateQueries(['forms', formId, 'stats'])
      },
      ...options
    })
  }

  const exportSubmissions = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, data }) => formsApi.submissions.export(formId, data),
      ...options
    })
  }

  // Integration Mutations
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

  // AI Mutations
  const generateForm = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.ai.generate(data),
      ...options
    })
  }

  const generateFields = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.ai.generateFields(data),
      ...options
    })
  }

  // Asset Upload Mutation
  const uploadAsset = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.assets.upload(data, options),
      ...options
    })
  }

  // Zapier Webhook Mutations
  const createZapierWebhook = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.zapier.store(data),
      ...options
    })
  }

  const deleteZapierWebhook = (options = {}) => {
    return useMutation({
      mutationFn: (id) => formsApi.zapier.delete(id),
      ...options
    })
  }

  // Utility functions
  const prefetchDetail = (slug) => {
    return queryClient.prefetchQuery({
      queryKey: ['forms', 'slug', slug],
      queryFn: () => formsApi.get(slug),
      staleTime: 5 * 60 * 1000
    })
  }

  const prefetchDetailById = (id) => {
    return queryClient.prefetchQuery({
      queryKey: ['forms', id],
      queryFn: () => formsApi.getById(id),
      staleTime: 5 * 60 * 1000
    })
  }

  const invalidateAll = () => {
    queryClient.invalidateQueries(['forms'])
  }

  const invalidateDetail = (id) => {
    queryClient.invalidateQueries(['forms', id])
  }

  const invalidateSubmissions = (formId) => {
    queryClient.invalidateQueries(['forms', formId, 'submissions'])
  }

  const invalidateStats = (formId) => {
    queryClient.invalidateQueries(['forms', formId, 'stats'])
    queryClient.invalidateQueries(['forms', formId, 'stats-details'])
  }

  const invalidateIntegrations = (formId) => {
    queryClient.invalidateQueries(['forms', formId, 'integrations'])
  }

  return {
    // Queries
    list,
    detail,
    detailById,
    paginatedList,
    submissions,
    paginatedSubmissions,
    submissionDetail,
    stats,
    statsDetails,
    integrations,
    integrationEvents,
    aiGeneration,
    
    // Form Mutations
    create,
    update,
    remove,
    duplicate,
    regenerateLink,
    updateWorkspace,
    
    // Submission Mutations
    submitForm,
    updateSubmission,
    deleteSubmission,
    exportSubmissions,
    
    // Integration Mutations
    createIntegration,
    updateIntegration,
    deleteIntegration,
    
    // AI Mutations
    generateForm,
    generateFields,
    
    // Other Mutations
    uploadAsset,
    createZapierWebhook,
    deleteZapierWebhook,
    
    // Utilities
    prefetchDetail,
    prefetchDetailById,
    invalidateAll,
    invalidateDetail,
    invalidateSubmissions,
    invalidateStats,
    invalidateIntegrations
  }
} 