import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { workspaceApi } from '~/api/workspace'

export function useWorkspaces() {
  const queryClient = useQueryClient()

  // Queries
  const list = (options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list', options.filters],
      queryFn: () => {
        return workspaceApi.list(options).then((data) => {
          data?.forEach((workspace) => {
            queryClient.setQueryData(['workspaces', workspace.id], workspace)
          })
          return data
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

  // Get current workspace helper
  const current = (options = {}) => {
    const workspacesStore = useWorkspacesStore()
    const currentId = computed(() => workspacesStore.currentId)
    
    return useQuery({
      queryKey: ['workspaces', 'current'],
      queryFn: () => {
        if (!currentId.value) return null
        
        // Try to get from cached list first
        const cachedWorkspaces = queryClient.getQueryData(['workspaces', 'list'])
        const cachedWorkspace = Array.isArray(cachedWorkspaces) 
          ? cachedWorkspaces.find(w => w.id === currentId.value)
          : null
          
        if (cachedWorkspace) {
          return cachedWorkspace
        }
        
        // Otherwise fetch individual workspace
        return opnFetch(`/open/workspaces/${currentId.value}`)
      },
      enabled: !!currentId.value,
      ...options
    })
  }

  // Paginated list with keepPreviousData
  const paginatedList = (page = ref(1), filters = ref({}), options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list', { page: page.value, ...filters.value }],
      queryFn: () => {
        return workspaceApi.list({ page: page.value, ...filters.value }).then((data) => {
          // Cache individual items from paginated response
          data?.data?.forEach((workspace) => {
            queryClient.setQueryData(['workspaces', workspace.id], workspace)
          })
          return data
        })
      },
      keepPreviousData: true,
      ...options
    })
  }



  // Mutations with manual cache updates
  const create = (options = {}) => {
    return useMutation({
      mutationFn: (data) => workspaceApi.create(data),
      onSuccess: (newWorkspace) => {
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
        
        // Update current workspace cache if this is the current one
        const workspacesStore = useWorkspacesStore()
        if (workspacesStore.currentId === newWorkspace.id) {
          queryClient.setQueryData(['workspaces', 'current'], newWorkspace)
        }
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
      onSuccess: (updatedWorkspace, { id }) => {
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
        
        // Update current workspace cache if this is the current one
        const workspacesStore = useWorkspacesStore()
        if (workspacesStore.currentId === id) {
          queryClient.setQueryData(['workspaces', 'current'], updatedWorkspace)
        }
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (id) => workspaceApi.delete(id),
      onSuccess: (_, deletedId) => {
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
        
        // Clear current workspace cache if this was the current one
        const workspacesStore = useWorkspacesStore()
        if (workspacesStore.currentId === deletedId) {
          queryClient.removeQueries(['workspaces', 'current'])
        }
      },
      ...options
    })
  }

  const leave = (options = {}) => {
    return useMutation({
      mutationFn: (workspaceId) => workspaceApi.leave(workspaceId),
      onSuccess: (_, leftWorkspaceId) => {
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
        
        // Clear current workspace cache if this was the current one
        const workspacesStore = useWorkspacesStore()
        if (workspacesStore.currentId === leftWorkspaceId) {
          queryClient.removeQueries(['workspaces', 'current'])
        }
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
        
        // Update current workspace cache if this is the current one
        const workspacesStore = useWorkspacesStore()
        if (workspacesStore.currentId === workspaceId) {
          queryClient.setQueryData(['workspaces', 'current'], (old) => {
            if (!old) return updatedWorkspace
            return { ...old, ...updatedWorkspace }
          })
        }
      },
      ...options
    })
  }

  // Utility functions
  const prefetchDetail = (id) => {
    return queryClient.prefetchQuery({
      queryKey: ['workspaces', id],
      queryFn: () => opnFetch(`/open/workspaces/${id}`)
    })
  }

  const prefetchCurrent = () => {
    const workspacesStore = useWorkspacesStore()
    if (!workspacesStore.currentId) return Promise.resolve(null)
    
    return queryClient.prefetchQuery({
      queryKey: ['workspaces', 'current'],
      queryFn: () => {
        // Try to get from cached list first
        const cachedWorkspaces = queryClient.getQueryData(['workspaces', 'list'])
        const cachedWorkspace = Array.isArray(cachedWorkspaces) 
          ? cachedWorkspaces.find(w => w.id === workspacesStore.currentId)
          : null
          
        if (cachedWorkspace) {
          return cachedWorkspace
        }
        
        return opnFetch(`/open/workspaces/${workspacesStore.currentId}`)
      }
    })
  }

  const invalidateAll = () => {
    queryClient.invalidateQueries(['workspaces'])
  }

  const invalidateDetail = (id) => {
    queryClient.invalidateQueries(['workspaces', id])
  }

  const invalidateCurrent = () => {
    queryClient.invalidateQueries(['workspaces', 'current'])
  }



  // Helper to get workspace from cache by ID
  const getWorkspaceById = (id) => {
    return queryClient.getQueryData(['workspaces', id])
  }

  return {
    // Queries
    list,
    detail,
    current,
    paginatedList,
    
    // Mutations
    create,
    update, 
    remove,
    leave,
    updateCustomDomains,
    
    // Utilities
    prefetchDetail,
    prefetchCurrent,
    invalidateAll,
    invalidateDetail,
    invalidateCurrent,
    getWorkspaceById
  }
} 