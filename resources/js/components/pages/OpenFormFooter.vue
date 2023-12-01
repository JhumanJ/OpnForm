<template>
  <div class="w-full">
    <div class="grid md:grid-cols-3 my-8">
      <div class="flex mt-2">
        <p class="text-sm text-gray-600 dark:text-gray-400 text-center w-full">
          © Copyright {{ currYear }}. All Rights Reserved
        </p>
      </div>
      <div class="flex justify-center mt-5 md:mt-0">
        <router-link :to="{ name: user ? 'home' : 'welcome' }" class="flex-shrink-0 font-semibold flex items-center">
          <img :src="asset('img/logo.svg')" alt="notion tools logo" class="w-10 h-10">
          <span class="ml-2 text-xl text-black dark:text-white">
            {{ appName }}
          </span>
        </router-link>
      </div>
      <ul class="flex justify-center mt-5 md:mt-1">
          <li class="mr-10">
            <router-link :to="{name:'privacy-policy'}" class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue">
              Privacy Policy
            </router-link>
          </li>
          <li class="list-disc pl-3">
            <router-link :to="{name:'terms-conditions'}" class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue">
              Terms & Conditions
            </router-link>
          </li>
        </ul>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../stores/auth'

export default {
  setup () {
    const authStore = useAuthStore()
    return {
      user : computed(() => authStore.user)
    }
  },

  data: () => ({
    appName: window.config.appName,
    currYear: new Date().getFullYear(),
  }),

  computed: {
    helpUrl: () => window.config.links.help_url,
    githubUrl: () => window.config.links.github_url,
    forumUrl: () => window.config.links.github_forum_url,
    changelogUrl: () => window.config.links.changelog_url,
    facebookGroupUrl: () => window.config.links.facebook_group,
    twitterUrl: () => window.config.links.twitter,
    featureRequestsUrl: () => window.config.links.feature_requests
  },

  mounted () {},

  methods: {}
}
</script>
