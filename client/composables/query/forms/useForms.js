import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useForms() {
  const queryClient = useQueryClient()

  const detail = (slug, options = {}) => {
    return useQuery({
      queryKey: ['forms', 'slug', slug],
      queryFn: () => formsApi.get(slug, options),
      enabled: !!slug,
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
      onSuccess: (form) => {
        if (form) {
          queryClient.setQueryData(['forms', 'slug', form.slug], form)
        }
      },
      ...options
    })
  }

  // Stats Queries
  const stats = (workspaceId, formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'stats'],
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

  // Form Mutations
  const create = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.create(data),
      onSuccess: (newForm) => {
        // Update workspace forms list
        queryClient.setQueriesData(['forms', 'listAll', newForm.workspace_id], (old) => {
          if (!Array.isArray(old) || old.length === 0) {
            return [newForm]
          }
          return [newForm, ...old]
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
        queryClient.setQueriesData(['forms', 'listAll'], (old) => {
          if (!Array.isArray(old)) return old
          return old.map(form =>
            form.id === id ? { ...form, ...updatedForm } : form
          )
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
        queryClient.setQueriesData(['forms', 'listAll'], (old) => {
          if (!Array.isArray(old)) return old
          return old.filter(form => form.id !== deletedId)
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
        queryClient.setQueriesData(['forms', 'listAll', duplicatedForm.workspace_id], (old) => {
          if (!Array.isArray(old) || old.length === 0) {
            return [duplicatedForm]
          }
          return [duplicatedForm, ...old]
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
          queryClient.setQueriesData(['forms', 'listAll', oldWorkspaceId], (old) => {
            if (!Array.isArray(old)) return old
            return old.filter(form => form.id !== id)
          })
        }
        
        // Add to new workspace list
        queryClient.setQueriesData(['forms', 'listAll', newWorkspaceId], (old) => {
          if (!Array.isArray(old) || old.length === 0) {
            return [updatedForm]
          }
          return [updatedForm, ...old]
        })
      },
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
      queryFn: () => formsApi.get(slug)
    })
  }

  const prefetchDetailById = (id) => {
    return queryClient.prefetchQuery({
      queryKey: ['forms', id],
      queryFn: () => formsApi.getById(id)
    })
  }

  const invalidateAll = () => {
    // Clear all forms list data first to make forms disappear immediately
    queryClient.resetQueries({ queryKey: ['forms', 'list'] })
    
    // Then invalidate all forms queries to trigger refetch
    queryClient.invalidateQueries({ queryKey: ['forms'] })
  }

  const invalidateDetail = (id) => {
    queryClient.invalidateQueries({ queryKey: ['forms', id] })
  }

  const invalidateStats = (formId) => {
    queryClient.invalidateQueries({ queryKey: ['forms', formId, 'stats'] })
    queryClient.invalidateQueries({ queryKey: ['forms', formId, 'stats-details'] })
  }

  return {
    // Queries
    detail,
    detailById,
    stats,
    statsDetails,
    
    // Form Mutations
    create,
    update,
    remove,
    duplicate,
    regenerateLink,
    updateWorkspace,
    
    // Other Mutations
    uploadAsset,
    createZapierWebhook,
    deleteZapierWebhook,
    
    // Utilities
    prefetchDetail,
    prefetchDetailById,
    invalidateAll,
    invalidateDetail,
    invalidateStats,
  }
} 