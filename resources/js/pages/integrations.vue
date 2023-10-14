<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl mb-4">
          Integrations
        </h1>
        <notion-page class="mb-8 integration-page" page-id="492c2bbb31404481b9faaaf407e59640" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import OpenFormFooter from '../components/pages/OpenFormFooter.vue'

export default {
  components: { OpenFormFooter },
  layout: 'default',

  props: {
    metaTitle: { type: String, default: 'Integrations' },
    metaDescription: { type: String, default: 'You can connect your OpnForms to other services via our two integrations: Zapier and Webhooks. Use our integrations to automate your various workflows.' }
  },

  data: () => ({
  }),

  computed: {},

  mounted () {
  }
}
</script>

<style lang="scss">
.integration-page {
  .notion-asset-wrapper {
    max-width: 200px;
  }
}
</style>
