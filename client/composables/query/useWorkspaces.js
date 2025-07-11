import { useQuery, useQueryClient, useMutation } from '@tanstack/vue-query'
import { workspaceApi } from '~/api'

export function useWorkspaces() {
  const queryClient = useQueryClient()

  // Queries
  const list = (options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list'],
      queryFn: () => { 
        return workspaceApi.list()
      },
      ...options
    })
  }

  const listWithSuspense = (options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list'],
      queryFn: () => workspaceApi.list(),
      staleTime: 5 * 60 * 1000, // 5 minutes
      suspense: true,
      onSuccess: (data) => {
        data?.forEach((workspace) => {
          queryClient.setQueryData(['workspaces', workspace.id], workspace)
        })
      },
      ...options
    })
  }

  const detail = (id, options = {}) => {
    return useQuery({
      queryKey: ['workspaces', id],
      queryFn: () => opnFetch(`/open/workspaces/${id}`),
      enabled: !!id,
      ...options
    })
  }

  // Mutations with manual cache updates
  const create = (options = {}) => {
    return useMutation({
      mutationFn: (data) => workspaceApi.create(data),
      onSuccess: (response) => {
        const newWorkspace = response.workspace
        // Cache the new item individually
        queryClient.setQueryData(['workspaces', newWorkspace.id], newWorkspace)
        
        // Add to list cache
        queryClient.setQueryData(['workspaces', 'list'], (old) => {
          if (!old) return [newWorkspace]
          if (!Array.isArray(old)) return old
          return [...old, newWorkspace]
        })
      },
      ...options
    })
  }

  const update = (options = {}) => {
    return useMutation({
      mutationFn: ({ id, data }) => opnFetch(`/open/workspaces/${id}`, { 
        method: 'PUT', 
        body: data 
      }),
      onSuccess: (response, { id }) => {
        const updatedWorkspace = response.workspace
        // Update individual item cache
        queryClient.setQueryData(['workspaces', id], response)
        
        // Update in list cache
        queryClient.setQueryData(['workspaces', 'list'], (old) => {
          if (!Array.isArray(old)) return old
          return old.map(workspace => 
            workspace.id === id ? { ...workspace, ...updatedWorkspace } : workspace
          )
        })
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (id) => workspaceApi.delete(id),
      onSuccess: (_, deletedId) => {
        // Remove from individual cache
        queryClient.removeQueries({ queryKey: ['workspaces', deletedId] })
        
        // Remove from list cache
        queryClient.setQueryData(['workspaces', 'list'], (old) => {
          if (!Array.isArray(old)) return old
          return old.filter(workspace => workspace.id !== deletedId)
        })
      },
      ...options
    })
  }

  const leave = (options = {}) => {
    return useMutation({
      mutationFn: (workspaceId) => workspaceApi.leave(workspaceId),
      onSuccess: () => {
        invalidate()
      },
      ...options
    })
  }

  // Custom domains mutation
  const updateCustomDomains = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: (data) => workspaceApi.customDomains.update(workspaceId, data),
      onSuccess: (updatedWorkspace) => {
        // Update individual workspace cache with custom domain info
        queryClient.setQueryData(['workspaces', workspaceId], (old) => {
          if (!old) return updatedWorkspace
          return { ...old, ...updatedWorkspace }
        })
      },
      ...options
    })
  }

  const invalidate = () => {
    queryClient.invalidateQueries({ queryKey: ['workspaces'] })
  }

  const invalidateAll = () => {
    queryClient.invalidateQueries({ queryKey: ['workspaces'] })
  }

  // Helper to get workspace from cache by ID
  const getWorkspaceById = (id) => {
    // Note: this is a non-reactive lookup
    return queryClient.getQueryData(['workspaces', id])
  }

  return {
    // Queries
    list,
    listWithSuspense,
    detail,
    
    // Mutations
    create,
    update, 
    remove,
    leave,
    updateCustomDomains,
    
    // Utilities
    invalidate,
    invalidateAll,
    getWorkspaceById,
  }
} 