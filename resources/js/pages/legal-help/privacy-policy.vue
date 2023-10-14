<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl">
          Privacy Policy
        </h1>
        <notion-page page-id="9c97349ceda7455aab9b341d1ff70f79" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: { OpenFormFooter},
  layout: 'default',
  mixins: [SeoMeta],
  
  data: () => ({
    metaTitle: 'Privacy Policy',
  }),

  computed: {},

  mounted () {
  }
}
</script>
