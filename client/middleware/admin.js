import {useAuthStore} from "../../resources/js/stores/auth.js";

export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  if (!authStore.user?.admin) {
    navigateTo({ name: 'home' })
  }
})
