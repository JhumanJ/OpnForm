export const useConfetti = () => {
  const nuxtApp = useNuxtApp()
  const $confetti = nuxtApp.vueApp.config.globalProperties.$confetti
  
  function play(duration=3000) {
    $confetti.start({ defaultSize: 6 })
    setTimeout(() => {
      $confetti.stop()
    }, duration)
  }

  return {
    play
  }
}
