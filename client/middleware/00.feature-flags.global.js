/**
 * Global middleware to load feature flags before other middleware runs
 * Runs first due to 00 prefix - ensures flags are available for other middleware
 */
export default defineNuxtRouteMiddleware(async () => {
  try {
    console.log('Loading feature flags...')
    const { flags } = useFeatureFlags()
    const query = flags()
    await query.suspense()
    
    // Check if flags were loaded
    const flagsData = query.data?.value
    console.log('Feature flags loaded:', flagsData)
    
  } catch (error) {
    console.warn('Feature flags middleware failed:', error)
  }
}) 