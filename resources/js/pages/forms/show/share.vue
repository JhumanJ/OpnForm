<template>
  <div>
    <share-link class="mt-4" :form="form" :extra-query-param="shareUrlForQueryParams" />

    <embed-code class="mt-6" :form="form" :extra-query-param="shareUrlForQueryParams" />

    <form-qr-code class="mt-6" :form="form" :extra-query-param="shareUrlForQueryParams" />

    <advanced-form-url-settings :form="form" v-model="shareFormConfig" />

    <div class="mt-6 pt-6 border-t w-full flex">
      <regenerate-form-link class="sm:w-1/2 mr-4" :form="form" />

      <url-form-prefill class="sm:w-1/2 mr-4" :form="form" :extra-query-param="shareUrlForQueryParams" />

      <embed-form-as-popup-modal class="sm:w-1/2" :form="form" />
    </div>
  </div>
</template>

<script>
import ShareLink from '../../../components/pages/forms/show/ShareLink.vue'
import EmbedCode from '../../../components/pages/forms/show/EmbedCode.vue'
import FormQrCode from '../../../components/pages/forms/show/FormQrCode.vue'
import UrlFormPrefill from '../../../components/pages/forms/show/UrlFormPrefill.vue'
import RegenerateFormLink from '../../../components/pages/forms/show/RegenerateFormLink.vue'
import SeoMeta from '../../../mixins/seo-meta.js'
import AdvancedFormUrlSettings from '../../../components/open/forms/components/AdvancedFormUrlSettings.vue'
import EmbedFormAsPopupModal from '../../../components/pages/forms/show/EmbedFormAsPopupModal.vue'

export default {
  components: {
    ShareLink,
    EmbedCode,
    FormQrCode,
    UrlFormPrefill,
    RegenerateFormLink,
    AdvancedFormUrlSettings,
    EmbedFormAsPopupModal
  },
  props: {
    form: { type: Object, required: true }
  },
  mixins: [SeoMeta],

  data: () => ({
    shareFormConfig: {
      hide_title: false,
      auto_submit: false
    }
  }),

  mounted() {},

  computed: {
    metaTitle() {
      return (this.form) ? 'Form Share - '+this.form.title : 'Form Share'
    },
    shareUrlForQueryParams () {
      let queryStr = ''
      for (const [key, value] of Object.entries(this.shareFormConfig)) {
        if(value && value !== 'false' && value !== false){
          queryStr += '&' + encodeURIComponent(key) + "=" + encodeURIComponent(value)
        }
      }
      return queryStr.slice(1)
    }
  },
}
</script>
