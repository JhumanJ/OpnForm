// Main composable - returns raw value (not computed)
export function useFeatureFlag(flagName, defaultValue = null) {
  // Server-side: use provided flags
  if (import.meta.server) {
    const nuxtApp = useNuxtApp()
    const flags = nuxtApp.$featureFlags || {}
    return getFlagFromObject(flags, flagName, defaultValue)
  }
  
  // Client-side: get current value from reactive state
  const featureFlags = useState('featureFlags', () => ({}))
  return getFlagFromObject(unref(featureFlags), flagName, defaultValue)
}

// Extended composable for flag management
export function useFeatureFlags() {
  const nuxtApp = useNuxtApp()
  
  return {
    // Get all flags 
    flags: () => {
      if (import.meta.server) {
        return {
          data: computed(() => nuxtApp.$featureFlags || {}),
          suspense: () => Promise.resolve() // No-op on server since already loaded
        }
      }
      
      // Client-side - return reactive reference
      return {
        data: useState('featureFlags', () => ({})),
        suspense: () => Promise.resolve() // No-op since loaded via SSR
      }
    },
    
    // Get individual flag (for backward compatibility)
    getFlag: (flagName, defaultValue = null) => {
      return useFeatureFlag(flagName, defaultValue)
    },
    
    // Invalidate/refresh flags from API (client-only)
    async invalidateFlags() {
      if (import.meta.server) {
        console.warn('Cannot invalidate feature flags on server-side')
        return
      }
      
      return await nuxtApp.$refreshFeatureFlags()
    }
  }
}

// Helper to extract flag value from nested object
function getFlagFromObject(flags, flagName, defaultValue) {
  if (!flags || typeof flags !== 'object') return defaultValue
  
  return flagName.split('.').reduce((acc, part) => {
    if (acc === undefined || acc === null) return defaultValue
    return acc && acc[part] !== undefined ? acc[part] : defaultValue
  }, flags)
}