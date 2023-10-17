<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16">
        <h1 class="sm:text-5xl mb-4">
          Tích hợp
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
    metaTitle: { type: String, default: 'Tích hợp' },
    metaDescription: { type: String, default: 'Bạn có thể kết nối OpnForms của mình với các dịch vụ khác thông qua hai tích hợp của chúng tôi: Zapier và Webhooks. Sử dụng tích hợp của chúng tôi để tự động hóa các quy trình làm việc đa dạng của bạn.' }
  },

  data: () => ({
  }),

  computed: {},

  mounted() {
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
