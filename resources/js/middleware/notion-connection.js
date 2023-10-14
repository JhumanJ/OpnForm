import store from '~/store'

export default async (to, from, next) => {
 /* if (store.getters['auth/check'] && store.getters['auth/user'].workspaces_count === 0) {
    if ([
      'forms.create',
      'forms.show',
      'forms.edit',
      'home'
    ].includes(to.name)
    ) {
      next({ name: 'onboarding' })
    }
  }*/

  next()
}
