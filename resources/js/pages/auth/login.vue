<template>
  <div>
    <div class="flex mt-6 mb-10">
      <div class="w-full md:max-w-6xl mx-auto px-4 flex md:flex-row-reverse flex-wrap">
        <div class="w-full md:w-1/2 md:p-6">
          <div class="border rounded-md p-6 shadow-md sticky top-4">
            <h2 class="font-semibold text-2xl">
              Đăng nhập vào OpnForm
            </h2>
            <small>Chào mừng trở lại! Vui lòng nhập thông tin của bạn.</small>

            <login-form />
          </div>
        </div>
        <div class="w-full md:w-1/2 md:p-6 mt-8 md:mt-0">
          <h1 class="font-bold">
            Tạo biểu mẫu đẹp và chia sẻ chúng mọi nơi
          </h1>
          <p class="text-gray-900 my-4 text-lg">
            Chỉ mất vài giây, bạn không cần biết cách lập trình và hoàn toàn miễn phí.
          </p>
          <div class="flex flex-wrap justify-center">
            <p class="px-3 pb-3 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Biểu mẫu không giới hạn
            </p>
            <p class="px-3 pb-3 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Trường không giới hạn
            </p>
            <p class="px-3 pb-3 text-sm text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
              Gửi không giới hạn
            </p>
          </div>
          <!--          <div class="mt-3 p-6">-->
          <!--            <testimonials />-->
          <!--          </div>-->
        </div>
      </div>
    </div>
    <open-form-footer />
  </div>
</template>

<script>
import OpenFormFooter from '../../components/pages/OpenFormFooter.vue'
import Testimonials from '../../components/pages/welcome/Testimonials.vue'
import LoginForm from './components/LoginForm.vue'
import SeoMeta from '../../mixins/seo-meta.js'

export default {
  components: {
    OpenFormFooter,
    Testimonials,
    LoginForm
  },

  middleware: 'guest',

  mixins: [SeoMeta],

  data: () => ({
    metaTitle: 'Đăng nhập',
  }),

  methods: {

  }
}
</script>
