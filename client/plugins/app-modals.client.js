export default defineNuxtPlugin(() => {
  // Initialize the shared composable globally
  // This ensures URL watchers are active across the entire app
  const appModals = useAppModals()
  
  // Make sure the composable is reactive to route changes
  // The watchers inside will handle URL -> modal synchronization
  return {
    provide: {
      appModals
    }
  }
}) 