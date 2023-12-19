import {useWorkspacesStore} from "~/stores/workspaces.js";

export default defineNuxtRouteMiddleware(async(to, from) => {
  const authStore = useAuthStore()
  authStore.initStore(useCookie('token').value, useCookie('admin_token').value)

  if (authStore.token && !authStore.user) {
    const {data, error} = await useOpnApi('user')
    authStore.setUser(data.value)
  }
})
