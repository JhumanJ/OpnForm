import { useAuthStore } from '../stores/auth';
import Cookies from 'js-cookie'

export default async (to, from, next) => {
  const authStore = useAuthStore()
  if (!authStore.check) {
    Cookies.set('intended_url', to.path)

    next({ name: 'login' })
  } else {
    next()
  }
}
