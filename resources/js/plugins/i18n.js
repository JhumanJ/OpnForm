import Vue from 'vue'
import store from '~/store'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

const i18n = new VueI18n({
  locale: 'en',
  messages: {}
})

/**
 * @param {String} locale
 */
export async function loadMessages (locale) {
  if (Object.keys(i18n.getLocaleMessage(locale)).length === 0) {
    if (locale) {
      const langFiles = import.meta.glob('../lang/**.json', {eager: true})
      const messages = langFiles[`../lang/${locale}.json`]
      i18n.setLocaleMessage(locale, messages)
    }
  }

  if (i18n.locale !== locale) {
    i18n.locale = locale
  }
}

;(async function () {
  await loadMessages(store.getters['lang/locale'])
})()

export default i18n
