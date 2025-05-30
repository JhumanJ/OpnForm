<template>
  <div class="w-full">
    <div class="grid md:grid-cols-3 my-8">
      <div class="flex mt-2 items-center">
        <p class="text-sm text-gray-600 dark:text-gray-400 text-center w-full">
          © Copyright {{ currYear }}. All Rights Reserved
          <span v-if="version">
            <br>Version {{ version }}
          </span>
        </p>
      </div>
      <div class="flex justify-center mt-5 md:mt-0">
        <router-link
          :to="{ name: user ? 'home' : 'index' }"
          class="flex-shrink-0 font-semibold flex items-center"
        >
          <img
            src="/img/logo.svg"
            alt="notion tools logo"
            class="w-10 h-10"
          >
          <span class="ml-2 text-xl text-black dark:text-white"> OpnForm </span>
        </router-link>
      </div>
      <div class="flex justify-center mt-5 md:mt-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-4 gap-y-2">
          <a
            :href="opnformConfig.links.feature_requests"
            target="_blank"
            class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
          >
            Feature Requests
          </a>
          <a
            :href="opnformConfig.links.roadmap"
            target="_blank"
            class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
          >
            Roadmap
          </a>
          <a
            :href="opnformConfig.links.discord"
            target="_blank"
            class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
          >
            Discord
          </a>
          <a
            :href="opnformConfig.links.tech_docs"
            target="_blank"
            class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
          >
            Technical Docs
          </a>
          <template v-if="!useFeatureFlag('self_hosted')">
            <router-link
              :to="{ name: 'integrations' }"
              class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
            >
              Integrations
            </router-link>
            <router-link
              :to="{ name: 'report-abuse' }"
              class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
            >
              Report Abuse
            </router-link>
            <router-link
              :to="{ name: 'privacy-policy' }"
              class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
            >
              Privacy Policy
            </router-link>

            <router-link
              :to="{ name: 'terms-conditions' }"
              class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
            >
              Terms & Conditions
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import opnformConfig from "~/opnform.config.js"

const authStore = useAuthStore()

const user = computed(() => authStore.user)
const currYear = ref(new Date().getFullYear())

// Use the reactive version for proper template reactivity
const version = computed(() => useFeatureFlag('version'))
</script>
