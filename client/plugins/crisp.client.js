import { Crisp } from "crisp-sdk-web"

export default defineNuxtPlugin(() => {
  const isIframe = useIsIframe()
  const crispWebsiteId = useRuntimeConfig().public.crispWebsiteId
  if (crispWebsiteId && !isIframe) {
    Crisp.configure(crispWebsiteId)
    window.Crisp = Crisp
  }
})
