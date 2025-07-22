import { getCurrentInstance } from 'vue'
import { useFeatureFlags } from './query/useFeatureFlags'

// Simple cache to store feature flags for non-Vue contexts
let flagsCache = {}

export function useFeatureFlag(flagName, defaultValue = null) {
  // If we're in Vue context, use the composable (reactive + fresh data)
  if (getCurrentInstance()) {
    try {
      const { getFlag } = useFeatureFlags()
      const value = getFlag(flagName, defaultValue)
      
      // Update cache with fresh value for future non-Vue context calls
      const currentFlags = useFeatureFlags().flags().data?.value
      if (currentFlags) {
        flagsCache = { ...currentFlags }
      }
      
      return value
    } catch {
      // Fallback to cache if composable fails
      return getFlagFromCache(flagName, defaultValue)
    }
  }
  
  // Not in Vue context - use cached values
  return getFlagFromCache(flagName, defaultValue)
}

// Helper to get flag from cache
function getFlagFromCache(flagName, defaultValue) {
  if (!flagsCache || Object.keys(flagsCache).length === 0) {
    return defaultValue
  }
  
  return flagName.split('.').reduce((acc, part) => {
    if (acc === undefined || acc === null) return defaultValue
    return acc && acc[part] !== undefined ? acc[part] : defaultValue
  }, flagsCache)
}

// Export cache updater for middleware to use
export function updateFeatureFlagsCache(flags) {
  flagsCache = { ...flags }
}