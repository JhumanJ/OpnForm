import { useQuery, useQueryClient, useMutation } from '@tanstack/vue-query'
import { workspaceApi } from '~/api'
import { chainCallbacks } from './index'

export function useWorkspaces() {
  const queryClient = useQueryClient()

  // Queries
  const list = (options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list'],
      queryFn: () => workspaceApi.list(),
      staleTime: 5 * 60 * 1000, // 5 minutes
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
    const builtInOnSuccess = (newWorkspace) => {
      // Update list cache with new item
      queryClient.setQueriesData(['workspaces', 'list'], (old) => {
        if (!old) return [newWorkspace]
        if (Array.isArray(old)) return [...old, newWorkspace]
        // Handle paginated response
        if (old.data) {
          return {
            ...old,
            data: [...old.data, newWorkspace]
          }
        }
        return old
      })
      // Cache the new item individually
      queryClient.setQueryData(['workspaces', newWorkspace.id], newWorkspace)
    }
    
    return useMutation({
      mutationFn: (data) => workspaceApi.create(data),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const update = (options = {}) => {
    const builtInOnSuccess = (updatedWorkspace, { id }) => {
      // Update individual item cache
      queryClient.setQueryData(['workspaces', id], updatedWorkspace)
      
      // Manually update all list caches instead of invalidating
      queryClient.setQueriesData(['workspaces', 'list'], (old) => {
        if (!old) return old
        if (Array.isArray(old)) {
          return old.map(workspace => 
            workspace.id === id ? { ...workspace, ...updatedWorkspace } : workspace
          )
        }
        // Handle paginated response
        if (old.data) {
          return {
            ...old,
            data: old.data.map(workspace => 
              workspace.id === id ? { ...workspace, ...updatedWorkspace } : workspace
            )
          }
        }
        return old
      })
    }
    
    return useMutation({
      mutationFn: ({ id, data }) => opnFetch(`/open/workspaces/${id}`, { 
        method: 'PUT', 
        body: data 
      }),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const remove = (options = {}) => {
    const builtInOnSuccess = (_, deletedId) => {
      // Remove from individual cache
      queryClient.removeQueries(['workspaces', deletedId])
      
      // Remove from all list caches
      queryClient.setQueriesData(['workspaces', 'list'], (old) => {
        if (!old) return old
        if (Array.isArray(old)) {
          return old.filter(workspace => workspace.id !== deletedId)
        }
        // Handle paginated response
        if (old.data) {
          return {
            ...old,
            data: old.data.filter(workspace => workspace.id !== deletedId)
          }
        }
        return old
      })
    }
    
    return useMutation({
      mutationFn: (id) => workspaceApi.delete(id),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const leave = (options = {}) => {
    const builtInOnSuccess = (_, leftWorkspaceId) => {
      // Remove from individual cache
      queryClient.removeQueries(['workspaces', leftWorkspaceId])
      
      // Remove from all list caches
      queryClient.setQueriesData(['workspaces', 'list'], (old) => {
        if (!old) return old
        if (Array.isArray(old)) {
          return old.filter(workspace => workspace.id !== leftWorkspaceId)
        }
        // Handle paginated response
        if (old.data) {
          return {
            ...old,
            data: old.data.filter(workspace => workspace.id !== leftWorkspaceId)
          }
        }
        return old
      })
    }
    
    return useMutation({
      mutationFn: (workspaceId) => workspaceApi.leave(workspaceId),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  // Custom domains mutation
  const updateCustomDomains = (workspaceId, options = {}) => {
    const builtInOnSuccess = (updatedWorkspace) => {
      // Update individual workspace cache with custom domain info
      queryClient.setQueryData(['workspaces', workspaceId], (old) => {
        if (!old) return updatedWorkspace
        return { ...old, ...updatedWorkspace }
      })
    }
    
    return useMutation({
      mutationFn: (data) => workspaceApi.customDomains.update(workspaceId, data),
      ...chainCallbacks(builtInOnSuccess, null, options)
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

  // Utility functions
  const prefetchList = (options = {}) => {
    return queryClient.prefetchQuery({
      queryKey: ['workspaces', 'list'],
      queryFn: () => workspaceApi.list(),
      staleTime: 5 * 60 * 1000, // 5 minutes
      ...options
    })
  }

  return {
    // Queries
    list,
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
    prefetchList,
  }
} 