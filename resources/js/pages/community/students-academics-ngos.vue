<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:max-w-3xl md:mx-auto px-4 md:pt-16 pb-10">
        <h1 class="sm:text-5xl mb-4">
          Ưu đãi e-Form cho Sinh viên, Giáo viên và Tổ chức Phi chính phủ
        </h1>
        <notion-page page-id="c65a499d39834e0b8978556a8d7af867" />
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'

export default {
  components: { OpenFormFooter },
  layout: 'default',

  props: {
    metaTitle: { type: String, default: ' Ưu đãi e-Form cho Sinh viên, Giáo viên và Tổ chức Phi chính phủ' },
    metaDescription: { type: String, default: 'Nếu bạn là sinh viên, giáo viên hoặc làm việc cho một tổ chức phi chính phủ (NGO), chúng tôi hân hạnh mang đến ưu đãi 40% cho đăng ký e-Form Pro của bạn.' }
  },

  data: () => ({
  }),

  computed: {},

  mounted() {
  }
}
</script>
