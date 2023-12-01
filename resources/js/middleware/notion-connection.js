import { useAuthStore } from '../stores/auth';

export default async (to, from, next) => {
  const authStore = useAuthStore()
  if (authStore.check && authStore.user?.workspaces_count === 0) {
    if ([
      'forms.create',
      'forms.show',
      'forms.edit',
      'home'
    ].includes(to.name)
    ) {
      next({ name: 'onboarding' })
      return
    }
  }

  next()
}
