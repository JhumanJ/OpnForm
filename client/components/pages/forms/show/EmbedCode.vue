<template>
  <div>
    <h3 class="font-semibold text-xl">Embed</h3>
    <p>Embed your form on your website by copying the HTML code below.</p>
    <copy-content :content="embedCode" buttonText="Copy Code">
      <template #icon>
        <svg class="h-4 w-4 -mt-1 text-blue-600 inline mr-1" viewBox="0 0 18 18" fill="none"
             xmlns="http://www.w3.org/2000/svg">
          <path
            d="M11.0833 11.5L13.5833 9L11.0833 6.5M6.91667 6.5L4.41667 9L6.91667 11.5M5.5 16.5H12.5C13.9001 16.5 14.6002 16.5 15.135 16.2275C15.6054 15.9878 15.9878 15.6054 16.2275 15.135C16.5 14.6002 16.5 13.9001 16.5 12.5V5.5C16.5 4.09987 16.5 3.3998 16.2275 2.86502C15.9878 2.39462 15.6054 2.01217 15.135 1.77248C14.6002 1.5 13.9001 1.5 12.5 1.5H5.5C4.09987 1.5 3.3998 1.5 2.86502 1.77248C2.39462 2.01217 2.01217 2.39462 1.77248 2.86502C1.5 3.3998 1.5 4.09987 1.5 5.5V12.5C1.5 13.9001 1.5 14.6002 1.77248 15.135C2.01217 15.6054 2.39462 15.9878 2.86502 16.2275C3.3998 16.5 4.09987 16.5 5.5 16.5Z"
            stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </template>
      Copy Code
    </copy-content>
  </div>
</template>

<script>
import CopyContent from '../../../open/forms/components/CopyContent.vue'
import {appUrl} from "~/lib/utils.js";

export default {
  name: 'EmbedCode',
  components: {CopyContent},
  props: {
    form: {type: Object, required: true},
    extraQueryParam: {type: String, default: ''}
  },

  data: () => ({
    autoresizeIframe: false
  }),

  computed: {
    embedCode() {
      return `
        <script type="text/javascript" src="${appUrl('/widgets/iframeResize.min.js')}"><\/script>
        ${this.iframeCode}
        <script type="text/javascript">iFrameResize({log: false, checkOrigin: false}, "#${this.iframeId}");<\/script>
`
    },
    iframeCode() {
      const share_url = (this.extraQueryParam) ? this.form.share_url + "?" + this.extraQueryParam : this.form.share_url + this.extraQueryParam
      return '<iframe style="border:none;width:100%;" id="' + this.iframeId + '" src="' + share_url + '"></iframe>'
    },
    iframeId() {
      return 'form-' + this.form.slug
    }
  },

  methods: {}
}
</script>
