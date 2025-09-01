import { useQueryClient, useQuery, useMutation, keepPreviousData } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useFormSubmissions() {
  const queryClient = useQueryClient()

  const submissions = (formId, options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'submissions', options.filters],
      queryFn: () => formsApi.submissions.list(formId, options),
      enabled: !!formId,
      onSuccess: (data) => {
        data?.forEach(submission => {
          queryClient.setQueryData(['submissions', submission.id], submission)
        })
      },
      ...options
    })
  }

  const paginatedSubmissions = (formId, page = ref(1), filters = ref({}), options = {}) => {
    return useQuery({
      queryKey: ['forms', formId, 'submissions', { page: page.value, ...filters.value }],
      queryFn: () => formsApi.submissions.list(formId, { page: page.value, ...filters.value }),
      enabled: !!formId,
      keepPreviousData: true,
      onSuccess: (data) => {
        data?.data?.forEach(submission => {
          queryClient.setQueryData(['submissions', submission.id], submission)
        })
      },
      ...options
    })
  }

  const paginatedList = (formId, options = {}) => {
    const page = ref(1)
    const search = ref('')
    const status = ref('all')
    const perPage = ref(100)

    // Debounced search (500ms)
    const debouncedSearch = refDebounced(search, 500)

    // Query key changes trigger refetch
    const queryKey = computed(() => [
      'forms',
      toValue(formId),
      'submissions',
      'paginated',
      page.value,
      debouncedSearch.value,
      status.value,
      perPage.value
    ])

    const query = useQuery({
      queryKey,
      queryFn: async () => {
        const params = {
          page: page.value,
          per_page: perPage.value
        }

        if (debouncedSearch.value) {
          params.search = debouncedSearch.value
        }

        if (status.value !== 'all') {
          params.status = status.value
        }

        return await formsApi.submissions.list(toValue(formId), { query: params })
      },
      placeholderData: keepPreviousData,
      staleTime: 30000, // 30 seconds
      enabled: computed(() => !!toValue(formId)),
      ...options
    })

    // Computed properties
    const submissions = computed(() =>
      query.data.value?.data?.map(record => record.data) || []
    )

    const pagination = computed(() => query.data.value?.meta || null)

    // Actions that reset to page 1
    const setSearch = (value) => {
      search.value = value
      page.value = 1
    }

    const setStatus = (value) => {
      status.value = value
      page.value = 1
    }

    const setPage = (value) => {
      page.value = value
    }

    // Reset when form changes
    watch(() => toValue(formId), () => {
      page.value = 1
      search.value = ''
      status.value = 'all'
    })

    return {
      // Data
      submissions,
      pagination,

      // States
      isLoading: query.isLoading,
      isFetching: query.isFetching,
      isError: query.isError,
      error: query.error,
      isPlaceholderData: query.isPlaceholderData,

      // Current values (readonly)
      page: readonly(page),
      search: readonly(search),
      status: readonly(status),

      // Actions
      setSearch,
      setStatus,
      setPage
    }
  }

  const submissionDetail = (slug, submissionId, options = {}) => {
    return useQuery({
      queryKey: ['submissions', submissionId],
      queryFn: () => formsApi.submissions.get(slug, submissionId, options),
      enabled: !!(slug && submissionId),
      ...options
    })
  }

  const updateSubmission = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, submissionId, data }) => formsApi.submissions.update(formId, submissionId, data),
      onSuccess: (updatedSubmission, { formId, submissionId }) => {
      // Update submission cache
      queryClient.setQueryData(['submissions', submissionId], updatedSubmission)
      
      // Update in submissions list
      queryClient.setQueryData(['forms', formId, 'submissions'], (old) => {
        if (!old) return old
        if (Array.isArray(old)) {
          return old.map(submission => 
            submission.id === submissionId ? { ...submission, ...updatedSubmission } : submission
          )
        }
        if (old.data) {
          return {
            ...old,
            data: old.data.map(submission => 
              submission.id === submissionId ? { ...submission, ...updatedSubmission } : submission
            )
          }
        }
        return old
      })
      },
      ...options
    })
  }

  const deleteSubmission = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, submissionId }) => formsApi.submissions.delete(formId, submissionId),
      onSuccess: (_, { formId, submissionId }) => {
      // Remove from submission cache
      queryClient.removeQueries({ queryKey: ['submissions', submissionId] })
      
      // Remove from submissions list
      queryClient.setQueryData(['forms', formId, 'submissions'], (old) => {
        if (!old) return old
        if (Array.isArray(old)) {
          return old.filter(submission => submission.id !== submissionId)
        }
        if (old.data) {
          return {
            ...old,
            data: old.data.filter(submission => submission.id !== submissionId)
          }
        }
        return old
      })
      
      // Invalidate stats
      queryClient.invalidateQueries(['forms', formId, 'stats'])
      },
      ...options
    })
  }

  const exportSubmissions = (options = {}) => {
    return useMutation({
      mutationFn: ({ formId, data }) => formsApi.submissions.export(formId, data),
      ...options
    })
  }

  const invalidateSubmissions = (formId) => {
    queryClient.invalidateQueries(['forms', formId, 'submissions'])
  }

  return {
    submissions,
    paginatedSubmissions,
    paginatedList,
    submissionDetail,
    updateSubmission,
    deleteSubmission,
    exportSubmissions,
    invalidateSubmissions,
  }
} 