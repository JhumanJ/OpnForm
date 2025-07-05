import { useQuery, useMutation } from '@tanstack/vue-query'
import { formsApi } from '~/api/forms'

export function useFormAI() {
  const aiGeneration = (generationId, options = {}) => {
    return useQuery({
      queryKey: ['ai', 'generation', generationId],
      queryFn: () => formsApi.ai.get(generationId, options),
      enabled: !!generationId,
      staleTime: 1 * 60 * 1000,
      refetchInterval: (data) => {
        // Poll if generation is in progress
        return data?.status === 'processing' ? 2000 : false
      },
      ...options
    })
  }

  const generateForm = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.ai.generate(data),
      ...options
    })
  }

  const generateFields = (options = {}) => {
    return useMutation({
      mutationFn: (data) => formsApi.ai.generateFields(data),
      ...options
    })
  }

  return {
    aiGeneration,
    generateForm,
    generateFields,
  }
} 