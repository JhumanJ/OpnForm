<template>
  <div class="flex flex-col min-h-full border-t">
    <section class="py-12 sm:py-16 bg-gray-50 border-b border-gray-200">
      <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center max-w-xl mx-auto">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900">
            Form Templates
          </h1>
          <p class="text-gray-600 mt-4 text-lg font-normal">
            Our collection of beautiful templates to create your own forms!
          </p>
        </div>
      </div>
    </section>

    <templates-list />

    <open-form-footer class="mt-8 border-t"/>
  </div>
</template>

<script>
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'
import TemplatesList from '../../components/pages/templates/TemplatesList.vue'
import SeoMeta from '../../mixins/seo-meta.js'

export default {

  components: { OpenFormFooter, TemplatesList },
  mixins: [SeoMeta],

  props: {
    metaTitle: { type: String, default: 'Templates' },
    metaDescription: { type: String, default: 'Our collection of beautiful templates to create your own forms!' }
  },

  data () {
    return {}
  },

  mounted () {},

  computed: {},

  methods: {}
}
</script>
