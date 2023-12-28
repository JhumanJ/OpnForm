import VueConfetti from 'vue-confetti'

export default defineNuxtPlugin(nuxtApp => {
  nuxtApp.vueApp.use(VueConfetti)
})