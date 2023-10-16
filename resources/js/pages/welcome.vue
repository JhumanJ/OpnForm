<template>
  <div>
    <section class="bg-gradient-to-b relative from-white to-gray-100 py-8 sm:py-16 ">
      <div class="absolute inset-0">
        <img class="w-full h-full object-cover object-top"
             :src="asset('img/pages/ai_form_builder/background-pattern.svg')" alt="">
      </div>

      <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto relative -mb-32 md:-mb-52 lg:-mb-72">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold text-gray-900 tracking-tight">
            Xây dựng
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-400">biểu mẫu</span>
            <br>
            nhanh chóng và miễn phí
          </h1>
          <p class="mt-4 sm:mt-5 text-base leading-7 sm:text-xl sm:leading-9 font-medium text-gray-500">
            Dễ dàng tạo cũng như chia sẻ biểu mẫu và biểu mẫu trực tuyến, đồng thời phân tích câu trả lời theo thời gian thực. Hoàn toàn
            <span class="font-semibold">miễn phí</span>!
          </p>

          <div class="mt-8 flex justify-center">
            <v-button v-if="!authenticated" class="mr-1" :to="{ name: 'forms.create.guest' }" :arrow="true">
              Tạo biểu mẫu MIỄN PHÍ
            </v-button>
            <v-button v-else class="mr-1" :to="{ name: 'forms.create' }" :arrow="true">
              Tạo biểu mẫu MIỄN PHÍ
            </v-button>
          </div>

          <div class="justify-center flex gap-2 mt-10">
            <div class="flex items-center text-gray-400 text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                   stroke="currentColor" class="w-4 h-4 mr-1 ticks">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
              <span>Không giới hạn số biểu mẫu</span>
            </div>
            <div class="flex items-center text-gray-400 text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                   stroke="currentColor" class="w-4 h-4 mr-1 ticks">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
              <span>
                  Không giới hạn số thuộc tính biểu mẫu
                </span>
            </div>
            <div class="flex text-gray-400 text-sm">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                   stroke="currentColor" class="w-4 h-4 mr-1 ticks">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
              <span>Không giới hạn số lượng trả lời biểu mẫu</span>
            </div>
          </div>
        </div>

        <div
          class="w-full mt-12 relative px-6 mx-auto max-w-4xl sm:px-10 lg:px-0 z-10 flex items-center justify-center">
          <div
            class="-m-2 rounded-xl bg-blue-900/5 p-2 backdrop-blur-sm ring-1 ring-inset ring-blue-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
            <img :src="asset('img/pages/welcome/product-cover.jpg')"
                 alt="Product screenshot" loading="lazy" class="rounded-md shadow-2xl ring-1 ring-gray-900/10">
          </div>
        </div>
      </div>
    </section>

    <div class="flex flex-col bg-gray-50 dark:bg-notion-dark">
      <div class="bg-white dark:bg-notion-dark-light pt-32 md:pt-52 lg:pt-72 pb-8">
        <div class="md:max-w-5xl md:mx-auto w-full">
          <features class="pb-8"/>
        </div>
      </div>

      <ai-feature class="bg-white -mb-56"/>

      <more-features class="pt-56"/>

      <pricing-table v-if="paidPlansEnabled" class="pb-20" :home-page="true">
        <template #pricing-table>
          <li class="flex gap-x-3">
            <router-link :to="{name:'pricing'}" class="flex gap-3">
              <div class="w-5"/>
              Tham khảo về giá
            </router-link>
          </li>
        </template>
      </pricing-table>

      <templates-slider class="max-w-full mb-12"/>

      <div class="w-full bg-blue-900 p-12 md:p-24 text-center">
        <h4 class="font-semibold text-3xl text-white">Đưa biểu mẫu/biểu mẫu của bạn lên tầm cao mới</h4>
        <p class="text-gray-300 my-8">Miễn phí - Không giới hạn</p>
        <div class="mt-6 flex justify-center">
          <v-button :to="{ name: 'forms.create.guest' }" v-track.welcome_create_form_click :arrow="true" color="blue">
            Tạo biểu mẫu miễn phí
          </v-button>
        </div>
        <div class="flex justify-center mt-6">   
          <a target="_blank" :href="configLinks.facebook_group" class="mr-4">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M24 12C24 5.37258 18.6274 0 12 0C5.37258 0 0 5.37258 0 12C0 17.9895 4.3882 22.954 10.125 23.8542V15.4688H7.07812V12H10.125V9.35625C10.125 6.34875 11.9166 4.6875 14.6576 4.6875C15.9701 4.6875 17.3438 4.92188 17.3438 4.92188V7.875H15.8306C14.34 7.875 13.875 8.80008 13.875 9.75V12H17.2031L16.6711 15.4688H13.875V23.8542C19.6118 22.954 24 17.9895 24 12Z"
                fill="currentColor"/>
            </svg>
          </a>
        </div>
      </div>

      <open-form-footer class="dark:border-t border-t"/>

    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import Features from '~/components/pages/welcome/Features.vue'
import MoreFeatures from '~/components/pages/welcome/MoreFeatures.vue'
import PricingTable from '../components/pages/pricing/PricingTable.vue'
import AiFeature from '~/components/pages/welcome/AiFeature.vue'
import OpenFormFooter from '../components/pages/OpenFormFooter.vue'
import Testimonials from '../components/pages/welcome/Testimonials.vue'
import TemplatesSlider from '../components/pages/welcome/TemplatesSlider.vue'
import SeoMeta from '../mixins/seo-meta.js'

export default {
  components: {Testimonials, OpenFormFooter, Features, MoreFeatures, PricingTable, AiFeature, TemplatesSlider},

  layout: 'default',

  mixins: [SeoMeta],

  data: () => ({
    title: window.config.appName,
    metaTitle: 'Tạo biểu mẫu dễ dàng và hoàn toàn miễn phí',
  }),

  mounted() {
  },

  methods: {
    // openCrisp () {
    //   window.$crisp.push(['do', 'chat:show'])
    //   window.$crisp.push(['do', 'chat:open'])
    //   window.$crisp.push(['do', 'message:send', ['text', "Hi! I'd like to learn more about OpnForm"]])
    // }
  },

  computed: {
    ...mapGetters({
      authenticated: 'auth/check'
    }),
    configLinks: () => window.config.links,
    paidPlansEnabled() {
      return window.config.paid_plans_enabled
    }
  }
}
</script>

<style lang="scss" scoped>

.customer-logo-container {
  max-width: 130px;
  width: 100%;
}

.ticks {
  color: #2563eb;
}

@screen md {
  #macbook-video {
    position: absolute;
    max-width: 84.8% !important;
    right: 0px;
    top: 6.8%;
  }
}
</style>
