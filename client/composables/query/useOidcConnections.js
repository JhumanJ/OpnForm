import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { oidcApi } from '~/api/oidc'

export function useOidcConnections(workspaceId) {
  const queryClient = useQueryClient()
  const alert = useAlert()

  // Resolve workspaceId value (handles both refs and plain values)
  const workspaceIdValue = computed(() => {
    return typeof workspaceId === 'function' ? workspaceId.value : workspaceId?.value ?? workspaceId
  })

  // Queries
  const connections = (options = {}) => {
    return useQuery({
      queryKey: ['oidc-connections', workspaceIdValue],
      queryFn: () => {
        const id = workspaceIdValue.value
        return id 
          ? oidcApi.list(id, options)
          : oidcApi.listGlobal(options)
      },
      enabled: computed(() => !!workspaceIdValue.value || workspaceIdValue.value === null),
      ...options
    })
  }

  const connection = (connectionId, options = {}) => {
    return useQuery({
      queryKey: ['oidc-connections', workspaceIdValue, connectionId],
      queryFn: () => {
        const id = workspaceIdValue.value
        return id
          ? oidcApi.get(id, connectionId)
          : oidcApi.getGlobal(connectionId)
      },
      enabled: computed(() => !!connectionId && (!!workspaceIdValue.value || workspaceIdValue.value === null)),
      ...options
    })
  }

  // Mutations
  const create = (options = {}) => {
    return useMutation({
      mutationFn: (data) => {
        const id = workspaceIdValue.value
        return id
          ? oidcApi.create(id, data)
          : oidcApi.createGlobal(data)
      },
      onSuccess: (newConnection) => {
        // Invalidate connections list
        queryClient.invalidateQueries({ queryKey: ['oidc-connections', workspaceIdValue] })
        // Cache individual connection
        queryClient.setQueryData(['oidc-connections', workspaceIdValue, newConnection.id], newConnection)
      },
      ...options
    })
  }

  const update = (options = {}) => {
    return useMutation({
      mutationFn: ({ connectionId, data }) => {
        const id = workspaceIdValue.value
        return id
          ? oidcApi.update(id, connectionId, data)
          : oidcApi.updateGlobal(connectionId, data)
      },
      onSuccess: (updatedConnection) => {
        // Update individual connection cache
        queryClient.setQueryData(['oidc-connections', workspaceIdValue, updatedConnection.id], updatedConnection)
        // Invalidate connections list
        queryClient.invalidateQueries({ queryKey: ['oidc-connections', workspaceIdValue] })
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (connectionId) => {
        const id = workspaceIdValue.value
        return id
          ? oidcApi.delete(id, connectionId)
          : oidcApi.deleteGlobal(connectionId)
      },
      onSuccess: (_, deletedConnectionId) => {
        // Remove from individual cache
        queryClient.removeQueries({ queryKey: ['oidc-connections', workspaceIdValue, deletedConnectionId] })
        // Invalidate connections list
        queryClient.invalidateQueries({ queryKey: ['oidc-connections', workspaceIdValue] })
      },
      ...options
    })
  }

  return {
    // Queries
    connections,
    connection,
    
    // Mutations
    create,
    update,
    remove,
  }
}

