import store from '~/store'
import * as Sentry from '@sentry/vue'

export function initCrisp (user) {
  return new Promise((resolve, reject) => {
    const intervalId = window.setInterval(function () {
      if (!user) {
        resolve()
        return
      }
      if (window.$crisp) {
        window.$crisp.push(['set', 'user:email', user.email])
        window.$crisp.push(['set', 'user:nickname', user.name])
        window.$crisp.push(['set', 'session:data', [[
          ['pro-subscription', user?.is_subscribed ?? false],
          ['id', user.id]
        ]]])
        window.clearInterval(intervalId)
        resolve()
      }
    }, 50000)
  })
}

export function initSentry (user) {
  if (!window.config.sentry_dsn || !user) {
    return
  }
  Sentry.configureScope((scope) => {
    scope.setUser({
      id: user.id,
      email: user.email,
      subscription: user?.is_subscribed
    })
  })
}

export default async (to, from, next) => {
  if (!store.getters['auth/check'] &&
    store.getters['auth/token'] !== null &&
    store.getters['auth/token'] !== undefined
  ) {
    try {
      const user = await store.dispatch('auth/fetchUser')
      initCrisp(user)
      initSentry(user)
    } catch (e) {
      console.log(e, 'error')
    }
  }
  next()
}
