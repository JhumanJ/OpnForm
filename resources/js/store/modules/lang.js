import Cookies from 'js-cookie'
import * as types from '../mutation-types'

const { locale, locales } = window.config

// state
export const state = {
  locale: getLocale(locales, locale),
  locales: locales
}

// getters
export const getters = {
  locale: state => state.locale,
  locales: state => state.locales
}

// mutations
export const mutations = {
  [types.SET_LOCALE] (state, { locale }) {
    state.locale = locale
  }
}

// actions
export const actions = {
  setLocale ({ commit }, { locale }) {
    commit(types.SET_LOCALE, { locale })

    Cookies.set('locale', locale, { expires: 365 })
  }
}

/**
 * @param  {String[]} locales
 * @param  {String} fallback
 * @return {String}
 */
function getLocale (locales, fallback) {
  const locale = Cookies.get('locale')

  if (Object.prototype.hasOwnProperty.call(locales, locale)) {
    return locale
  } else if (locale) {
    Cookies.remove('locale')
  }

  return fallback
}
