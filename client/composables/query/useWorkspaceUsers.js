import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { unref, computed } from 'vue'
import { workspaceApi } from '~/api/workspace'

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
    return useMutation({
      mutationFn: (userData) => workspaceApi.users.add(unref(workspaceId), userData),
      onSuccess: (newInvite) => {
        // Update invites cache since /users/add actually creates an invitation
        queryClient.setQueryData(['workspaces', unref(workspaceId), 'invites'], (old) => {
          if (!old) return [newInvite]
          return [...old, newInvite]
        })
      },
      ...options
    })
  }

  const removeUser = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: (userId) => workspaceApi.users.remove(unref(workspaceId), userId),
      onSuccess: (_, removedUserId) => {
        // Update users cache for this workspace
        queryClient.setQueryData(['workspaces', unref(workspaceId), 'users'], (old) => {
          if (!old) return old
          return old.filter(user => user.id !== removedUserId)
        })
      },
      ...options
    })
  }

  const updateUserRole = (workspaceId, options = {}) => {
    return useMutation({
      mutationFn: ({ userId, data }) => workspaceApi.users.updateRole(unref(workspaceId), userId, data),
      onSuccess: (updatedUser, { userId }) => {
        // Update users cache for this workspace
        queryClient.setQueryData(['workspaces', unref(workspaceId), 'users'], (old) => {
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
      mutationFn: (inviteId) => workspaceApi.invites.resend(unref(workspaceId), inviteId),
      onSuccess: (updatedInvite, inviteId) => {
        // Update invites cache for this workspace
        queryClient.setQueryData(['workspaces', unref(workspaceId), 'invites'], (old) => {
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
      mutationFn: (inviteId) => workspaceApi.invites.cancel(unref(workspaceId), inviteId),
      onSuccess: (_, cancelledInviteId) => {
        // Update invites cache for this workspace
        queryClient.setQueryData(['workspaces', unref(workspaceId), 'invites'], (old) => {
          if (!old) return old
          return old.filter(invite => invite.id !== cancelledInviteId)
        })
      },
      ...options
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