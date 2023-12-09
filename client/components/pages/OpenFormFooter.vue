<template>
  <div class="w-full">
    <div class="grid md:grid-cols-3 my-8">
      <div class="flex mt-2">
        <p class="text-sm text-gray-600 dark:text-gray-400 text-center w-full">
          © Copyright {{ currYear }}. All Rights Reserved
        </p>
      </div>
      <div class="flex justify-center mt-5 md:mt-0">
        <router-link :to="{ name: user ? 'home' : 'index' }" class="flex-shrink-0 font-semibold flex items-center">
          <img src="/img/logo.svg" alt="notion tools logo" class="w-10 h-10">

          <span class="ml-2 text-xl text-black dark:text-white">
            OpnForm
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
    currYear: new Date().getFullYear(),
  }),

  computed: {
    helpUrl: () => this.$config.links.help_url,
    githubUrl: () => this.$config.links.github_url,
    forumUrl: () => this.$config.links.github_forum_url,
    changelogUrl: () => this.$config.links.changelog_url,
    facebookGroupUrl: () => this.$config.links.facebook_group,
    twitterUrl: () => this.$config.links.twitter,
    featureRequestsUrl: () => this.$config.links.feature_requests
  },

  mounted () {},

  methods: {}
}
</script>
