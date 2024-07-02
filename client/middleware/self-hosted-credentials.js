export default defineNuxtRouteMiddleware((from, to, next) => {
  const authStore = useAuthStore()
  const runtimeConfig = useRuntimeConfig()
    if (runtimeConfig.public?.selfHosted) {
        if (authStore.check && !authStore.user?.credentials_changed) {
            return navigateTo({ name: "update-credentials" })
        }
    }
})
