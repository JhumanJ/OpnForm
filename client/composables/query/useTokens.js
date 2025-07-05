import { useQueryClient, useQuery, useMutation } from '@tanstack/vue-query'
import { tokensApi } from '~/api/tokens'

export function useTokens() {
  const queryClient = useQueryClient()

  // Queries
  const list = (options = {}) => {
    return useQuery({
      queryKey: ['tokens', 'list', options.filters],
      queryFn: () => tokensApi.list(options),
      onSuccess: (data) => {
        data?.forEach(token => {
          queryClient.setQueryData(['tokens', token.id], token)
        })
      },
      ...options
    })
  }

  const detail = (tokenId, options = {}) => {
    return useQuery({
      queryKey: ['tokens', tokenId],
      queryFn: () => {
        // Since there's no individual get endpoint, we get from the cached list
        const cachedTokens = queryClient.getQueryData(['tokens', 'list'])
        return cachedTokens?.find(t => t.id === tokenId) || null
      },
      enabled: !!tokenId,
      ...options
    })
  }

  // Paginated list
  const paginatedList = (page = ref(1), filters = ref({}), options = {}) => {
    return useQuery({
      queryKey: ['tokens', 'list', { page: page.value, ...filters.value }],
      queryFn: () => tokensApi.list({ page: page.value, ...filters.value }),
      keepPreviousData: true,
      onSuccess: (data) => {
        data?.data?.forEach(token => {
          queryClient.setQueryData(['tokens', token.id], token)
        })
      },
      ...options
    })
  }

  // Mutations
  const create = (options = {}) => {
    return useMutation({
      mutationFn: (data) => tokensApi.create(data),
      onSuccess: (newToken) => {
        // Update tokens list
        queryClient.setQueriesData(['tokens', 'list'], (old) => {
          if (!old) return [newToken]
          if (Array.isArray(old)) return [newToken, ...old]
          if (old.data) {
            return {
              ...old,
              data: [newToken, ...old.data]
            }
          }
          return old
        })
        // Cache the new token
        queryClient.setQueryData(['tokens', newToken.id], newToken)
      },
      ...options
    })
  }

  const remove = (options = {}) => {
    return useMutation({
      mutationFn: (tokenId) => tokensApi.delete(tokenId),
      onSuccess: (_, deletedTokenId) => {
        // Remove from individual cache
        queryClient.removeQueries(['tokens', deletedTokenId])
        
        // Remove from tokens lists
        queryClient.setQueriesData(['tokens', 'list'], (old) => {
          if (!old) return old
          if (Array.isArray(old)) {
            return old.filter(token => token.id !== deletedTokenId)
          }
          if (old.data) {
            return {
              ...old,
              data: old.data.filter(token => token.id !== deletedTokenId)
            }
          }
          return old
        })
      },
      ...options
    })
  }

  // Utility functions
  const prefetchList = () => {
    return queryClient.prefetchQuery({
      queryKey: ['tokens', 'list'],
      queryFn: () => tokensApi.list()
    })
  }

  const invalidateAll = () => {
    queryClient.invalidateQueries(['tokens'])
  }

  const invalidateDetail = (tokenId) => {
    queryClient.invalidateQueries(['tokens', tokenId])
  }

  const invalidateList = () => {
    queryClient.invalidateQueries(['tokens', 'list'])
  }

  const getTokenById = (tokenId) => {
    return queryClient.getQueryData(['tokens', tokenId])
  }

  const getTokenByName = (name) => {
    const tokens = queryClient.getQueryData(['tokens', 'list'])
    if (Array.isArray(tokens)) {
      return tokens.find(t => t.name === name) || null
    }
    if (tokens?.data) {
      return tokens.data.find(t => t.name === name) || null
    }
    return null
  }

  return {
    // Queries
    list,
    detail,
    paginatedList,
    
    // Mutations
    create,
    remove,
    
    // Utilities
    prefetchList,
    invalidateAll,
    invalidateDetail,
    invalidateList,
    getTokenById,
    getTokenByName
  }
} 