import { useInfiniteQuery } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'
import { watchEffect, computed } from 'vue'
import { useQueryClient } from '@tanstack/vue-query'

export function useFormsList(workspaceId, options = {}) {
  // Separate API filters and custom options from TanStack Query options
  const { filters, fetchAll, ...queryOptions } = options

  const queryClient = useQueryClient()

  const query = useInfiniteQuery({
    queryKey: ['forms', 'list', workspaceId.value, filters],
    queryFn: ({ pageParam = 1 }) => {
      // Ensure only 'filters' are passed, not all queryOptions
      const apiFilters = { page: pageParam, ...(filters || {}) }
      return formsApi.list(workspaceId.value, { params: apiFilters }).then(res => {
        // Prime cache for each form
        res?.data?.forEach(form => {
          queryClient.setQueryData(['forms', form.id], form)
          if (form.slug) {
            queryClient.setQueryData(['forms', 'slug', form.slug], form)
          }
        })
        return res
      })
    },
    initialPageParam: 1,
    getNextPageParam: (lastPage) => {
      if (!lastPage?.meta) {
        console.warn('`meta` property not found in lastPage response. Cannot determine next page.')
        return undefined
      }
      const { current_page, last_page } = lastPage.meta
      const nextPage = current_page < last_page ? current_page + 1 : undefined
      return nextPage
    },
    ...queryOptions,
  })

  // If fetchAll is true, automatically fetch all pages
  if (fetchAll) {
    watchEffect(() => {
      if (
        query.isSuccess.value &&
        query.hasNextPage.value &&
        !query.isFetchingNextPage.value
      ) {
        query.fetchNextPage()
      }
    })
  }
    
  // Computed values for easier access
  const forms = computed(() => 
    query.data.value?.pages?.filter(page => page !== null)?.flatMap(page => page.data) || []
  )
  const currentPage = computed(() => {
    const pages = query.data.value?.pages
    if (!pages || pages.length === 0) return 0
    const lastPage = pages[pages.length - 1]
    return lastPage?.meta?.current_page || pages.length
  })
  const totalPages = computed(() => query.data.value?.pages?.[0]?.meta?.last_page || 1)
  const isComplete = computed(() => !query.hasNextPage.value)

  return {
    ...query,
    forms,
    currentPage,
    totalPages,
    isComplete,
  }
} 