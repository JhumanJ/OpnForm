export default defineNuxtRouteMiddleware(() => {
  const authStore = useAuthStore()

  if (authStore.check) {
    console.log("redirecting to home")
    return navigateTo({ name: "home" })
  }
})
