import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'
import { chainCallbacks } from '../index'
import { useIsAuthenticated } from '~/composables/useAuthFlow'
import { useFormsListCache } from './useFormsList'

export function useForms() {
  const queryClient = useQueryClient()
  const { isAuthenticated } = useIsAuthenticated()
  const formsListCache = useFormsListCache()

  const detail = (slug, options = {}) => {
    return useQuery({
      queryKey: ['forms', 'slug', slug],
      queryFn: () => {
        if (isAuthenticated.value) {
          return formsApi.get(slug, options)
        }
        return formsApi.publicGet(slug, options)
      },
      enabled: !!slug,
      onSuccess: (form) => {
        if (form) {
          queryClient.setQueryData(['forms', form.id], form)
        }
      },
      ...options,
    })
  }

  const detailById = (id, options = {}) => {
    return useQuery({
      queryKey: ['forms', id],
      queryFn: () => {
        if (isAuthenticated.value) {
          return formsApi.getById(id, options)
        }
        return formsApi.publicGetById(id, options)
      },
      enabled: !!id,
      onSuccess: (form) => {
        if (form) {
          queryClient.setQueryData(['forms', 'slug', form.slug], form)
        }
      },
      ...options,
    })
  }

  // Form Mutations
  const create = (options = {}) => {
    const builtInOnSuccess = (response) => {
      const newForm = response.form
      formsListCache.add(newForm.workspace_id, newForm)
      // Cache the new form
      queryClient.setQueryData(['forms', newForm.id], newForm)
      queryClient.setQueryData(['forms', 'slug', newForm.slug], newForm)
    }
    
    return useMutation({
      mutationFn: (data) => formsApi.create(data),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const update = (options = {}) => {
    const builtInOnSuccess = (updatedForm, { id }) => {
      const form = updatedForm.form

      // Update individual form cache
      queryClient.setQueryData(['forms', id], form)
      if (form.slug) {
        queryClient.setQueryData(['forms', 'slug', form.slug], form)
      }
      
      // Update in workspace lists
      formsListCache.update(form.workspace_id, form)
    }
    
    return useMutation({
      mutationFn: ({ id, data }) => formsApi.update(id, data),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const remove = (options = {}) => {
    const builtInOnSuccess = (_, deletedId) => {      
      const deletedForm = queryClient.getQueryData(['forms', deletedId])
      if (!deletedForm) return

      invalidateDetail(deletedForm)

      // Remove from workspace lists
      formsListCache.remove(deletedForm.workspace_id, deletedId)
    }
    
    return useMutation({
      mutationFn: (id) => formsApi.delete(id),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const duplicate = (options = {}) => {
    const builtInOnSuccess = (response) => {
      const duplicatedForm = response.new_form
      // Add to workspace forms list
      formsListCache.add(duplicatedForm.workspace_id, duplicatedForm)
      // Cache the duplicated form
      queryClient.setQueryData(['forms', duplicatedForm.id], duplicatedForm)
      queryClient.setQueryData(['forms', 'slug', duplicatedForm.slug], duplicatedForm)
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
      
      // Remove from old workspace list
      if (oldWorkspaceId) {
        formsListCache.remove(oldWorkspaceId, id)
      }

      // Add to new workspace list
      formsListCache.add(newWorkspaceId, updatedForm)
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
    // Clear all forms list data first to make forms disappear immediately
    queryClient.invalidateQueries({ queryKey: ['forms', 'list'] })
    
    // Then invalidate all forms queries to trigger refetch
    queryClient.invalidateQueries({ queryKey: ['forms'] })
  }

  const invalidateDetail = (form) => {
    if (form.id) {
      queryClient.invalidateQueries({ queryKey: ['forms', form.id] })
    }
    if (form.slug) {
      queryClient.invalidateQueries({ queryKey: ['forms', 'slug', form.slug] })
    }
  }

  return {
    // Queries
    detail,
    detailById,

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
    invalidateDetail
  }
} 