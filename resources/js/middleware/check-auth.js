import store from '~/store'

export function initCrisp (user) {
  return new Promise((resolve, reject) => {
    const intervalId = window.setInterval(function () {
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

export default async (to, from, next) => {
  if (!store.getters['auth/check'] &&
    store.getters['auth/token'] !== null &&
    store.getters['auth/token'] !== undefined
  ) {
    try {
      const user = await store.dispatch('auth/fetchUser')
      initCrisp(user)
    } catch (e) {
      console.log(e, 'error')
    }
  }
  next()
}
