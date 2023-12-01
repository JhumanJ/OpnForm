import { useAuthStore } from '../stores/auth';

export default (to, from, next) => {
  const authStore = useAuthStore()
  if (!authStore.user?.is_subscribed) {
    next({ name: 'pricing' })
  } else {
    next()
  }
}
