export default defineNuxtPlugin(() => {
  useAppModals()
  
  return {
    provide: {
      // Provide a lazy getter that initializes on first access
      get appModals() {
        return useAppModals()
      }
    }
  }
}) 