import { useAuthStore } from '../stores/auth';

export default (to, from, next) => {
  const authStore = useAuthStore()
  if (!authStore.user?.admin) {
    next({ name: 'home' })
  } else {
    next()
  }
}
