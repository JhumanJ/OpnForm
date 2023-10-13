<template>
  <div class="flex flex-col min-h-full border-t">
    <section class="py-12 sm:py-16 bg-gray-50 border-b border-gray-200">
      <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center max-w-xl mx-auto">
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-gray-900">
            My Form Templates
          </h1>
          <p class="text-gray-600 mt-4 text-lg font-normal">
            Share your best form as templates so that others can re-use them!
          </p>
        </div>
      </div>
    </section>

    <templates-list :only-my="true" />
  </div>
</template>

<script>
import TemplatesList from '../../components/pages/templates/TemplatesList.vue'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: { TemplatesList },
  mixins: [SeoMeta],
  middleware: 'auth',

  props: {
    metaTitle: { type: String, default: 'My Templates' },
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
