import {useAuthStore} from "../../resources/js/stores/auth.js";

export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  if (!authStore.check) {
    useCookie('intended_url').value = to.path

    navigateTo({ name: 'login' })
  }
})
