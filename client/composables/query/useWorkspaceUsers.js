import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { unref, computed } from 'vue'
import { workspaceApi } from '~/api/workspace'
import { chainCallbacks } from './index'

export function useWorkspaceUsers() {
  const queryClient = useQueryClient()

  // Queries
  const users = (workspaceId, options = {}) => {
    return useQuery({
      queryKey: ['workspaces', workspaceId, 'users'],
      queryFn: () => workspaceApi.users.list(unref(workspaceId)),
      enabled: computed(() => !!unref(workspaceId)),
      ...options
    })
  }

  const invites = (workspaceId, options = {}) => {
    return useQuery({
      queryKey: ['workspaces', workspaceId, 'invites'],
      queryFn: () => workspaceApi.invites.list(unref(workspaceId)),
      enabled: computed(() => !!unref(workspaceId)),
      ...options
    })
  }

  // Current workspace users and invites helpers
  const currentUsers = computed(() => {
    const workspacesStore = useWorkspacesStore()
    return users(workspacesStore.currentId)
  })

  const currentInvites = computed(() => {
    const workspacesStore = useWorkspacesStore()
    return invites(workspacesStore.currentId)
  })

  // User management mutations
  const addUser = (workspaceId, options = {}) => {
    const builtInOnSuccess = (response) => {
      // Built-in cache management
      // Extract invite from response if it exists, otherwise use the full response
      const newInvite = response.invite || response
      queryClient.setQueryData(['workspaces', unref(workspaceId), 'invites'], (old) => {
        if (!old) return [newInvite]
        if (!Array.isArray(old)) return old
        return [...old, newInvite]
      })
    }
    
    return useMutation({
      mutationFn: (userData) => workspaceApi.users.add(unref(workspaceId), userData),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const removeUser = (workspaceId, options = {}) => {
    const builtInOnSuccess = (data, removedUserId) => {
      // Built-in cache management
      queryClient.setQueryData(['workspaces', unref(workspaceId), 'users'], (old) => {
        if (!Array.isArray(old)) return old
        return old.filter(user => user.id !== removedUserId)
      })
    }
    
    return useMutation({
      mutationFn: (userId) => workspaceApi.users.remove(unref(workspaceId), userId),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const updateUserRole = (workspaceId, options = {}) => {
    const builtInOnSuccess = (updatedUser, { userId }) => {
      // Built-in cache management
      queryClient.setQueryData(['workspaces', unref(workspaceId), 'users'], (old) => {
        if (!Array.isArray(old)) return old
        return old.map(user =>
          user.id === userId ? { ...user, ...updatedUser } : user
        )
      })
    }
    
    return useMutation({
      mutationFn: ({ userId, data }) => workspaceApi.users.updateRole(unref(workspaceId), userId, data),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  // Invite management mutations
  const resendInvite = (workspaceId, options = {}) => {
    const builtInOnSuccess = (updatedInvite, inviteId) => {
      // Built-in cache management
      queryClient.setQueryData(['workspaces', unref(workspaceId), 'invites'], (old) => {
        if (!Array.isArray(old)) return old
        return old.map(invite =>
          invite.id === inviteId ? { ...invite, ...updatedInvite } : invite
        )
      })
    }
    
    return useMutation({
      mutationFn: (inviteId) => workspaceApi.invites.resend(unref(workspaceId), inviteId),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  const cancelInvite = (workspaceId, options = {}) => {
    const builtInOnSuccess = (data, cancelledInviteId) => {
      // Built-in cache management
      queryClient.setQueryData(['workspaces', unref(workspaceId), 'invites'], (old) => {
        if (!Array.isArray(old)) return old
        return old.filter(invite => invite.id !== cancelledInviteId)
      })
    }
    
    return useMutation({
      mutationFn: (inviteId) => workspaceApi.invites.cancel(unref(workspaceId), inviteId),
      ...chainCallbacks(builtInOnSuccess, null, options)
    })
  }

  // Utility functions
  const invalidateUsers = (workspaceId) => {
    queryClient.invalidateQueries(['workspaces', workspaceId, 'users'])
  }

  const invalidateInvites = (workspaceId) => {
    queryClient.invalidateQueries(['workspaces', workspaceId, 'invites'])
  }

  const invalidateCurrentUsers = () => {
    const workspacesStore = useWorkspacesStore()
    if (workspacesStore.currentId) {
      invalidateUsers(workspacesStore.currentId)
    }
  }

  const invalidateCurrentInvites = () => {
    const workspacesStore = useWorkspacesStore()
    if (workspacesStore.currentId) {
      invalidateInvites(workspacesStore.currentId)
    }
  }

  return {
    // Queries
    users,
    invites,
    currentUsers,
    currentInvites,
    
    // Mutations
    addUser,
    removeUser,
    updateUserRole,
    resendInvite,
    cancelInvite,
    
    // Utilities
    invalidateUsers,
    invalidateInvites,
    invalidateCurrentUsers,
    invalidateCurrentInvites
  }
} 