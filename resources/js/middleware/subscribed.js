import store from '~/store'

export default (to, from, next) => {
  if (!store.getters['auth/user'].is_subscribed) {
    next({ name: 'pricing' })
  } else {
    next()
  }
}
