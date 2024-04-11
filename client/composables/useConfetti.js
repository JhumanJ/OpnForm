import { ref, onUnmounted } from "vue"

export const useConfetti = () => {
  let timeoutId = ref(null)
  const nuxtApp = useNuxtApp()
  const $confetti = nuxtApp.vueApp.config.globalProperties.$confetti

  function play(duration = 3000) {
    $confetti.start({ defaultSize: 6 })
    timeoutId.value = setTimeout(() => {
      $confetti.stop()
    }, duration)
  }

  onUnmounted(() => {
    if (timeoutId.value) clearTimeout(timeoutId)
  })

  return {
    play,
  }
}
