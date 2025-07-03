<template>
    <copy-content
      :content="embedCode"
      label="Copy Code"
    />
</template>

<script>
/* eslint-disable */
import CopyContent from "../../../open/forms/components/CopyContent.vue"
import { appUrl } from "~/lib/utils.js"

export default {
  name: "EmbedCode",
  components: { CopyContent },
  props: {
    form: { type: Object, required: true },
    extraQueryParam: { type: String, default: "" },
  },

  data: () => ({
    autoresizeIframe: false,
  }),

  computed: {
    embedCode() {
      // eslint-disable no-useless-escape
      return `${this.iframeCode}<script type="text/javascript" onload="initEmbed('${this.form.slug}')" src="${appUrl("/widgets/iframe.min.js")}"><\/script>`
    },
    iframeCode() {
      const share_url = this.extraQueryParam
        ? this.form.share_url + "?" + this.extraQueryParam
        : this.form.share_url + this.extraQueryParam
      return (
        '<iframe style="border:none;width:100%;" id="' + this.form.slug + '" src="' + share_url + '"></iframe>'
      )
    }
  },

  methods: {},
}
</script>
