<template>
  <div class="w-full">
    <div class="grid md:grid-cols-3 my-8">
      <div class="flex mt-2 items-center">
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
      <div class="flex justify-center mt-5 md:mt-0">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-4 gap-y-2">
          <a href="https://opnform.featurebase.app/" target="_blank"
             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue">
            Feature Requests
          </a>
          <a href="https://opnform.featurebase.app/roadmap" target="_blank"
             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue">
            Roadmap
          </a>
          <router-link :to="{name:'privacy-policy'}"
                       class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue">
            Privacy Policy
          </router-link>

          <router-link :to="{name:'terms-conditions'}"
                       class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue">
            Terms & Conditions
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
  data: () => ({
    appName: window.config.appName,
    currYear: new Date().getFullYear(),
  }),

  computed: {
    ...mapGetters({
      user: 'auth/user'
    }),
    helpUrl: () => window.config.links.help_url,
    githubUrl: () => window.config.links.github_url,
    forumUrl: () => window.config.links.github_forum_url,
    changelogUrl: () => window.config.links.changelog_url,
    facebookGroupUrl: () => window.config.links.facebook_group,
    twitterUrl: () => window.config.links.twitter,
    featureRequestsUrl: () => window.config.links.feature_requests
  },

  mounted() {
  },

  methods: {}
}
</script>
