import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'
import { chainCallbacks } from './index'

export function useForms() {
  const queryClient = useQueryClient()

  // Queries
  const detail = (slug, options = {}) => {
    const slugValue = toRef(slug).value
    return useQuery({
      queryKey: ['forms', 'slug', slugValue],
      queryFn: () => formsApi.get(slugValue),
      enabled: !!slug,
      staleTime: 10 * 60 * 1000, // 10 minutes
      cacheTime: 30 * 60 * 1000, // 30 minutes
      onSuccess: (form) => {
        queryClient.setQueryData(['forms', form.id], form)
      },
      ...options
    })
  }

  const detailById = (id, options = {}) => {
    const idValue = toRef(id).value
    return useQuery({
      queryKey: ['forms', idValue],
      queryFn: () => formsApi.getById(idValue),
      enabled: !!id,
      staleTime: 10 * 60 * 1000, // 10 minutes
      cacheTime: 30 * 60 * 1000, // 30 minutes
      onSuccess: (form) => {
        queryClient.setQueryData(['forms', 'slug', form.slug], form)
      },
      ...options
    })
  }

  // Form Mutations
  const create = (options = {}) => {
    const builtInOnSuccess = (newForm) => {
      // Update workspace forms list (legacy)
      queryClient.setQueriesData(['forms', 'listAll', newForm.workspace_id], (old) => {
        if (!Array.isArray(old) || old.length === 0) {
          return [newForm]
        }
        return [newForm, ...old]
      })
      // Cache the new form
      queryClient.setQueryData(['forms', newForm.id], newForm)
      queryClient.setQueryData(['forms', 'slug', newForm.slug], newForm)
      
      // Invalidate forms list queries (used by useFormsList.js)
      queryClient.invalidateQueries({ queryKey: ['forms', 'list'] })
    }
    
    return useMutation({
      mutationFn: (data) => formsApi.create(data),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const update = (options = {}) => {
    const builtInOnSuccess = (response, { id }) => {
      // Update individual form cache
      const updatedForm = response.form
      queryClient.setQueryData(['forms', id], updatedForm)
      if (updatedForm.slug) {
        queryClient.setQueryData(['forms', 'slug', updatedForm.slug], updatedForm)
      }
      
      // Update in workspace lists (legacy)
      queryClient.setQueriesData(['forms', 'listAll'], (old) => {
        if (!Array.isArray(old)) return old
        return old.map(form =>
          form.id === id ? { ...form, ...updatedForm } : form
        )
      })
      
      // Invalidate forms list queries (used by useFormsList.js)
      queryClient.invalidateQueries({ queryKey: ['forms', 'list'] })
    }
    
    return useMutation({
      mutationFn: ({ id, data }) => formsApi.update(id, data),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const remove = (options = {}) => {
    const builtInOnSuccess = (_, deletedId) => {
      const deletedForm = queryClient.getQueryData(['forms', deletedId])
      
      // Remove from all caches
      queryClient.removeQueries(['forms', deletedId])
      if (deletedForm?.slug) {
        queryClient.removeQueries(['forms', 'slug', deletedForm.slug])
      }
      queryClient.removeQueries(['forms', deletedId, 'submissions'])
      queryClient.removeQueries(['forms', deletedId, 'stats'])
      
      // Remove from workspace lists (legacy)
      queryClient.setQueriesData(['forms', 'listAll'], (old) => {
        if (!Array.isArray(old)) return old
        return old.filter(form => form.id !== deletedId)
      })
      
      // Invalidate forms list queries (used by useFormsList.js)
      queryClient.invalidateQueries({ queryKey: ['forms', 'list'] })
    }
    
    return useMutation({
      mutationFn: (id) => formsApi.delete(id),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const duplicate = (options = {}) => {
    const builtInOnSuccess = (duplicatedForm) => {
      // Add to workspace forms list (legacy)
      queryClient.setQueriesData(['forms', 'listAll', duplicatedForm.workspace_id], (old) => {
        if (!Array.isArray(old) || old.length === 0) {
          return [duplicatedForm]
        }
        return [duplicatedForm, ...old]
      })
      // Cache the duplicated form
      queryClient.setQueryData(['forms', duplicatedForm.id], duplicatedForm)
      queryClient.setQueryData(['forms', 'slug', duplicatedForm.slug], duplicatedForm)
      
      // Invalidate forms list queries (used by useFormsList.js)
      queryClient.invalidateQueries({ queryKey: ['forms', 'list'] })
    }
    
    return useMutation({
      mutationFn: (id) => formsApi.duplicate(id),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const regenerateLink = (options = {}) => {
    const builtInOnSuccess = (updatedForm, { id }) => {
      queryClient.setQueryData(['forms', id], (old) => {
        return old ? { ...old, ...updatedForm } : updatedForm
      })
      if (updatedForm.slug) {
        queryClient.setQueryData(['forms', 'slug', updatedForm.slug], (old) => {
          return old ? { ...old, ...updatedForm } : updatedForm
        })
      }
    }
    
    return useMutation({
      mutationFn: ({ id, option }) => formsApi.regenerateLink(id, option),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const updateWorkspace = (options = {}) => {
    const builtInOnSuccess = (updatedForm, { id, workspaceId: newWorkspaceId }) => {
      const oldForm = queryClient.getQueryData(['forms', id])
      const oldWorkspaceId = oldForm?.workspace_id
      
      // Update form cache
      queryClient.setQueryData(['forms', id], updatedForm)
      if (updatedForm.slug) {
        queryClient.setQueryData(['forms', 'slug', updatedForm.slug], updatedForm)
      }
      
      // Remove from old workspace list (legacy)
      if (oldWorkspaceId) {
        queryClient.setQueriesData(['forms', 'listAll', oldWorkspaceId], (old) => {
          if (!Array.isArray(old)) return old
          return old.filter(form => form.id !== id)
        })
      }
      
      // Add to new workspace list (legacy)
      queryClient.setQueriesData(['forms', 'listAll', newWorkspaceId], (old) => {
        if (!Array.isArray(old) || old.length === 0) {
          return [updatedForm]
        }
        return [updatedForm, ...old]
      })
      
      // Invalidate forms list queries (used by useFormsList.js)
      queryClient.invalidateQueries({ queryKey: ['forms', 'list'] })
    }
    
    return useMutation({
      mutationFn: ({ id, workspaceId, data }) => formsApi.updateWorkspace(id, workspaceId, data),
      ...chainCallbacks(builtInOnSuccess, null, options)
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
    queryClient.invalidateQueries({ queryKey: ['forms'] })
  }

  const invalidateDetail = (id) => {
    queryClient.invalidateQueries({ queryKey: ['forms', id] })
  }

  const invalidateDetailBySlug = (slug) => {
    queryClient.invalidateQueries({ queryKey: ['forms', 'slug', slug] })
  }

  return {
    // Queries
    detail,
    detailById,
    
    // Mutations
    create,
    update,
    remove,
    duplicate,
    regenerateLink,
    updateWorkspace,
    uploadAsset,
    createZapierWebhook,
    deleteZapierWebhook,
    
    // Utilities
    prefetchDetail,
    prefetchDetailById,
    invalidateAll,
    invalidateDetail,
    invalidateDetailBySlug
  }
} 