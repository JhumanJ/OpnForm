import {Crisp} from "crisp-sdk-web"
import config from "~/opnform.config.js";

export default defineNuxtPlugin(nuxtApp => {
  const isIframe = useIsIframe()
  if (config.crisp_website_id && !isIframe) {
    Crisp.configure(config.crisp_website_id)
    window.Crisp = Crisp
  }
});
