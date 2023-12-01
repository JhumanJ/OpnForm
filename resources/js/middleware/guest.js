import { useAuthStore } from '../stores/auth';

export default (to, from, next) => {
  const authStore = useAuthStore()
  if (authStore.check) {
    next({ name: 'home' })
  } else {
    next()
  }
}
