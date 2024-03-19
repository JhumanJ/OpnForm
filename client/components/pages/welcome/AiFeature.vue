<template>
    <div class="py-8">
      <section>
        <div class="mx-auto max-w-7xl isolate sm:px-6 lg:px-8">
          <div
            class="relative px-4 py-8 overflow-hidden bg-blue-100 ring-blue-100 ring-1 sm:shadow-lg isolate sm:rounded-2xl sm:px-16 md:pt-20 lg:flex lg:gap-x-20 lg:px-16 lg:py-0 sm:shadow-gray-600/10">
            <div class="absolute inset-0">
              <img class="object-cover object-top w-full h-full" src="/img/pages/ai_form_builder/background-pattern-ai.svg" alt=""/>
            </div>

            <div class="relative max-w-md mx-auto text-center xl:max-w-lg lg:mx-0 lg:flex-auto lg:py-16 lg:text-left">
              <span
                class="bg-white text-xs font-semibold inline-flex items-center shadow-sm ring-blue-200 ring-1 text-blue-600 px-2.5 py-1.5 rounded-full">
                <svg aria-hidden="true" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                  fill="currentColor">
                  <path fill-rule="evenodd"
                    d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z"
                    clip-rule="evenodd" />
                </svg>
                Introducing OpnForm AI
              </span>

              <h2 class="mt-6 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl lg:text-4xl">
                Say goodbye to tedious form building with OpnForm's new <span
                  class="text-transparent bg-clip-text bg-gradient-to-r lg:block from-blue-600 to-blue-300">AI-powered
                  feature!</span>
              </h2>
              <p class="mt-4 text-base font-medium leading-7 text-gray-500 sm:text-lg sm:leading-8">
                Easily generate a fully working form in seconds with just a simple description.
              </p>

              <div class="flex justify-center lg:justify-start mt-4">
              <v-button class="block" v-track.welcome_ai_builder_click :to="{ name: 'forms-create-guest' }" :arrow="true" color="blue">
                Try our AI form builder
              </v-button>
              </div>

              <ul
                class="flex flex-wrap items-center justify-center mt-4 gap-4 text-sm font-medium text-gray-900 lg:justify-start">
                <li class="flex items-center gap-2">
                  <svg aria-hidden="true" class="w-5 h-5 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd" />
                  </svg>
                  Create form in minutes
                </li>

                <li class="flex items-center gap-2">
                  <svg aria-hidden="true" class="w-5 h-5 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd" />
                  </svg>
                  Customizations
                </li>

                <li class="flex items-center gap-2">
                  <svg aria-hidden="true" class="w-5 h-5 text-gray-400 shrink-0" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd" />
                  </svg>
                  No-coding required
                </li>
              </ul>

              <div class="mt-6 sm:mt-8 flex text-center justify-center lg:justify-start">
<!--                <v-button v-if="!authenticated" class="mr-2 block" :to="{ name: 'forms-create-guest' }" :arrow="true">-->
<!--                  Get started for free-->
<!--                </v-button>-->
<!--                <v-button v-else class="mr-2 block" :to="{ name: 'forms-create' }" :arrow="true">-->
<!--                  Get started for free-->
<!--                </v-button>-->
<!--                <v-button color="light-gray" class="mr-1 block" :to="{ name: 'aiformbuilder' }">-->
<!--                  Learn more-->
<!--                </v-button>-->
              </div>
            </div>

            <div
              class="relative hidden lg:block w-full px-6 mx-auto mt-8 lg:px-0 sm:mt-12 lg:bottom-0 lg:absolute lg:-right-16 xl:right-0 lg:max-w-lg">
              <img class="rounded-t-2xl ring-1 ring-blue-100 lg:rounded-tr-none w-full"
              src="/img/pages/ai_form_builder/ai-feature-illustration.svg" alt="App screenshot"/>

            </div>
          </div>
        </div>
      </section>
    </div>
</template>

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'

export default {
  setup () {
    const authStore = useAuthStore()
    return {
      authenticated : computed(() => authStore.check)
    }
  },
  props: {},
  data: () => ({}),
  computed: {},
  methods: {}
}
</script>
