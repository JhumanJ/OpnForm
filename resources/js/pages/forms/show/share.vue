<template>
  <div>
    <!-- Settings -->
    <h3 class="font-semibold text-xl mt-4">Settings</h3>
    <toggle-switch-input name="hide_title" class="mt-4"
        label="Hide Form Title"
        :disabled="form.hide_title===true"
        @input="onChangeHideTitle"
        :help="hideTitleHelp"
    />

    <share-link class="mt-6" :form="form" :hide-title="hideTitle" />

    <embed-code class="mt-6" :form="form" :hide-title="hideTitle" />

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
import SeoMeta from '../../../mixins/seo-meta'

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
  mixins: [SeoMeta],

  data: () => ({
    hideTitle: false
  }),

  mounted() {},

  computed: {
    metaTitle() {
      return (this.form) ? 'Form Share - '+this.form.title : 'Form Share'
    },
    hideTitleHelp () {
      return this.form.hide_title ? 'This option is disabled because the form title is already hidden' : null
    }
  },

  methods: {
    onChangeHideTitle (val) {
      this.hideTitle = val;
    }
  }
}
</script>