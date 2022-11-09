<template>
  <div>
    <share-link class="mt-4" :form="form" />

    <embed-code class="mt-6" :form="form" />

    <div class="mt-6 pt-6 border-t w-full flex">
      <regenerate-form-link class="sm:w-1/2 mr-4" :form="form" />

      <url-form-prefill class="sm:w-1/2" :form="form" />
    </div>
  </div>
</template>

<script>
import ShareLink from '../../../components/pages/forms/show/ShareLink'
import EmbedCode from '../../../components/pages/forms/show/EmbedCode'
import UrlFormPrefill from '../../../components/pages/forms/show/UrlFormPrefill'
import RegenerateFormLink from '../../../components/pages/forms/show/RegenerateFormLink'

export default {
  components: {
    ShareLink,
    EmbedCode,
    UrlFormPrefill,
    RegenerateFormLink
  },
  props: {
    form: { type: Object, required: true }
  },

  metaInfo() {
    return {title: 'Form Share'}
  },

  data: () => ({
  }),

  mounted() {},

  computed: {},

  methods: {
    
  }
}
</script>