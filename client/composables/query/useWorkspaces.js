import { workspaceApi } from '~/api/workspace'

export function useWorkspaces() {
  const queryClient = useQueryClient()

  // Queries
  const list = (options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list', options.filters],
      queryFn: () => workspaceApi.list(options),
      staleTime: 5 * 60 * 1000,
      // Cache individual items from list response
      onSuccess: (data) => {
        data?.forEach(workspace => {
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
      staleTime: 5 * 60 * 1000,
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
      staleTime: 5 * 60 * 1000,
      ...options
    })
  }

  // Paginated list with keepPreviousData
  const paginatedList = (page = ref(1), filters = ref({}), options = {}) => {
    return useQuery({
      queryKey: ['workspaces', 'list', { page: page.value, ...filters.value }],
      queryFn: () => workspaceApi.list({ page: page.value, ...filters.value }),
      keepPreviousData: true,
      staleTime: 5 * 60 * 1000,
      onSuccess: (data) => {
        // Cache individual items from paginated response
        data?.data?.forEach(workspace => {
          queryClient.setQueryData(['workspaces', workspace.id], workspace)
        })
      },
      ...options
    })
  }

  const users = (workspaceId, options = {}) => {
    return useQuery({
      queryKey: ['workspaces', workspaceId, 'users'],
      queryFn: () => workspaceApi.users.list(workspaceId),
      enabled: !!workspaceId,
      staleTime: 2 * 60 * 1000,
      ...options
    })
  }

  const invites = (workspaceId, options = {}) => {
    return useQuery({
      queryKey: ['workspaces', workspaceId, 'invites'],
      queryFn: () => workspaceApi.invites.list(workspaceId),
      enabled: !!workspaceId,
      staleTime: 2 * 60 * 1000,
      ...options
    })
  }

  // Current workspace users and invites helpers
  const currentUsers = (options = {}) => {
    const workspacesStore = useWorkspacesStore()
    return users(computed(() => workspacesStore.currentId), options)
  }

  const currentInvites = (options = {}) => {
    const workspacesStore = useWorkspacesStore()
    return invites(computed(() => workspacesStore.currentId), options)
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

  // User management mutations
  const addUser = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: (userData) => workspaceApi.users.add(workspaceId, userData),
      onSuccess: (newUser) => {
        // Update users cache for this workspace
        queryClient.setQueryData(['workspaces', workspaceId, 'users'], (old) => {
          if (!old) return [newUser]
          return [...old, newUser]
        })
      },
      ...options
    })
  }

  const removeUser = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: (userId) => workspaceApi.users.remove(workspaceId, userId),
      onSuccess: (_, removedUserId) => {
        // Update users cache for this workspace
        queryClient.setQueryData(['workspaces', workspaceId, 'users'], (old) => {
          if (!old) return old
          return old.filter(user => user.id !== removedUserId)
        })
      },
      ...options
    })
  }

  const updateUserRole = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: ({ userId, data }) => workspaceApi.users.updateRole(workspaceId, userId, data),
      onSuccess: (updatedUser, { userId }) => {
        // Update users cache for this workspace
        queryClient.setQueryData(['workspaces', workspaceId, 'users'], (old) => {
          if (!old) return old
          return old.map(user => 
            user.id === userId ? { ...user, ...updatedUser } : user
          )
        })
      },
      ...options
    })
  }

  // Invite management mutations
  const resendInvite = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: (inviteId) => workspaceApi.invites.resend(workspaceId, inviteId),
      onSuccess: (updatedInvite, inviteId) => {
        // Update invites cache for this workspace
        queryClient.setQueryData(['workspaces', workspaceId, 'invites'], (old) => {
          if (!old) return old
          return old.map(invite => 
            invite.id === inviteId ? { ...invite, ...updatedInvite } : invite
          )
        })
      },
      ...options
    })
  }

  const cancelInvite = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: (inviteId) => workspaceApi.invites.cancel(workspaceId, inviteId),
      onSuccess: (_, cancelledInviteId) => {
        // Update invites cache for this workspace
        queryClient.setQueryData(['workspaces', workspaceId, 'invites'], (old) => {
          if (!old) return old
          return old.filter(invite => invite.id !== cancelledInviteId)
        })
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
      queryFn: () => opnFetch(`/open/workspaces/${id}`),
      staleTime: 5 * 60 * 1000
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
      },
      staleTime: 5 * 60 * 1000
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

  const invalidateUsers = (workspaceId) => {
    queryClient.invalidateQueries(['workspaces', workspaceId, 'users'])
  }

  const invalidateInvites = (workspaceId) => {
    queryClient.invalidateQueries(['workspaces', workspaceId, 'invites'])
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
    users,
    invites,
    currentUsers,
    currentInvites,
    
    // Mutations
    create,
    update, 
    remove,
    leave,
    addUser,
    removeUser,
    updateUserRole,
    resendInvite,
    cancelInvite,
    updateCustomDomains,
    
    // Utilities
    prefetchDetail,
    prefetchCurrent,
    invalidateAll,
    invalidateDetail,
    invalidateCurrent,
    invalidateUsers,
    invalidateInvites,
    getWorkspaceById
  }
} 