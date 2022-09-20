<template>
  <div>
    <div class="mt-6 flex flex-col">
      <div class="w-full md:mx-auto flex flex-wrap pb-10 md:pb-20 md:pt-10 relative max-w-5xl">
        <div
          class="flex flex-col justify-center w-full lg:w-1/2 text-center lg:text-left lg:pr-10 p-6 lg:p-10 relative z-10"
        >
          <h1 dusk="title" class="text-4xl lg:text-5xl">
            The Open-Source <br>
            <span
              class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600"
            >Form Builder</span><br>
          </h1>
          <h3 class="mt-6 text-xl text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
            Create beautiful forms and share them anywhere. It takes seconds, you don't need to know how to code
            and <span
              class="font-semibold"
            >it's free</span>.
          </h3>
          <p class="mt-6">
            <router-link :to="{ name: 'register' }">
              <v-button v-track.welcome_create_form_click>
                Create an OpnForm for free
              </v-button>
            </router-link>
          </p>
        </div>
        <div class="w-full lg:w-1/2 relative px-6 sm:px-10 lg:px-0 z-10 lg:pr-10 flex items-center justify-center">
          <img loading="lazy" class="w-full shadow-xl rounded-lg block max-w-2xl lg:max-w-5xl"
               :src="asset('img/pages/welcome/product_cover.jpg')" alt="cover-product">
        </div>
        <div class="z-0 absolute bottom-0 inset-x-0 z-0 w-full opacity-30 dark:opacity-70">
          <img class="mx-auto" :src="asset('img/pages/welcome/homebackdrop.png')" alt="Backdrop decoration image">
        </div>
      </div>

      <div class="bg-gray-50 dark:bg-notion-dark-light pb-8">
        <div class="w-full md:max-w-4xl md:mx-auto px-4 flex flex-wrap">
          <features class="pt-16 pb-8" />
          <p class="text-center w-full mb-8 font-semibold">
            And much more!
          </p>
          <p class="mt-4 text-center w-full">
            <router-link :to="{ name: 'register' }">
              <v-button v-track.welcome_create_form_click>
                Create an OpnForm for free
              </v-button>
            </router-link>
          </p>
        </div>
      </div>

      <div class="w-full dark:bg-notion-dark-light">
        <div class="w-full bg-blue-500 p-10 relative">
          <div class="md:max-w-3xl md:mx-auto flex flex-wrap relative z-10">
            <div class="flex items-center">
              <div class="text-2xl font-bold">
                <h3 class="text-white">
                  The contact form below is an <span class="text-gray-900">OpnForm</span>.
                  It can be created for free and it uses open-source code!
                </h3>
              </div>
              <img class="w-32 hidden md:block md:ml-6" loading="lazy" :src="asset('img/icons/sparks.png')" alt="contact-form">
            </div>
          </div>
        </div>
        <div class="w-full dark:bg-notion-dark">
          <div class="md:max-w-3xl md:mx-auto px-4 flex flex-wrap pt-6">
            <iframe class="w-full" height="450px" src="https://opnform.com/forms/my-form" />
          </div>
        </div>
        <open-form-footer class="dark:border-t border-t" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Features from '~/components/pages/welcome/Features'
import OpenFormFooter from '../components/pages/OpenFormFooter'
import Testimonials from '../components/pages/welcome/Testimonials'

export default {
  components: { Testimonials, OpenFormFooter, Features },

  layout: 'default',

  metaInfo () {
    return { title: 'Create beautiful & open-source forms for free' }
  },

  data: () => ({
    title: window.config.appName
  }),

  mounted () {},

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
    })
  }
}
</script>

<style lang="scss" scoped>

.customer-logo-container {
  max-width: 130px;
  width: 100%;
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
