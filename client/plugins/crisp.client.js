import {Crisp} from "crisp-sdk-web"
import config from "~/opnform.config.js";

export default defineNuxtPlugin(nuxtApp => {
  const isIframe = useIsIframe()
  const crispWebsiteId = useRuntimeConfig().public.crispWebsiteId
  if (crispWebsiteId && !isIframe) {
    Crisp.configure(crispWebsiteId)
    window.Crisp = Crisp
  }
});
