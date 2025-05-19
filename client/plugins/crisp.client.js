import { Crisp } from "crisp-sdk-web"

export default defineNuxtPlugin(() => {
  const isIframe = useIsIframe()
  const crispWebsiteId = useRuntimeConfig().public.crispWebsiteId
  const isPublicFormPage = useRoute().name === 'forms-slug'
  
  if (crispWebsiteId && !isIframe && !isPublicFormPage) {
    Crisp.configure(crispWebsiteId)
    window.Crisp = Crisp
  }
})
