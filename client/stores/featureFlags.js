import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useFeatureFlagsStore = defineStore('feature_flags', () => {
  const flags = ref({})

  async function fetchFlags() {
    try {
      const { data } = await useOpnApi('content/feature-flags')
      flags.value = data.value
    } catch (error) {
      console.error('Failed to fetch feature flags:', error)
    }
  }

  function getFlag(path, defaultValue = false) {
    return path.split('.').reduce((acc, part) => {
      if (acc === undefined) return defaultValue
      return acc && acc[part] !== undefined ? acc[part] : defaultValue
    }, flags.value)
  }

  return { flags, fetchFlags, getFlag }
})