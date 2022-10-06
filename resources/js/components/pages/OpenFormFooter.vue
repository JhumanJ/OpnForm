<template>
  <div class="w-full bg-gray-50 dark:bg-notion-dark">
    <div class="grid md:grid-cols-3 my-10">
      <div class="flex">
        <p class="text-sm text-gray-600 dark:text-gray-400 text-center w-full">
          © Copyright 2022. All Rights Reserved
        </p>
      </div>
      <div class="flex font-bold justify-center">
        <a href="/" aria-label="Go home" title="Company">
            <img :src="asset('img/logo.svg')" alt="notion tools logo" class="w-8 h-8 inline">
            <span class="ml-2 text-xl font-bold tracking-wide text-gray-800 dark:text-gray-200">OpnForm</span>
          </a>
      </div>
      <ul class="flex">
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
export default {
  data: () => ({}),

  computed: {
    helpUrl: () => window.config.links.help_url,
    githubUrl: () => window.config.links.github_url,
    forumUrl: () => window.config.links.github_forum_url,
    changelogUrl: () => window.config.links.changelog_url,
    facebookGroupUrl: () => window.config.links.facebook_group,
    twitterUrl: () => window.config.links.twitter,
    featureRequestsUrl: () => window.config.links.feature_requests
  },

  mounted () {
    this.loadCannyChangelog()
  },

  methods: {
    loadCannyChangelog () {
      this.$loadScript('https://canny.io/sdk.js')
        .then(() => {
          window.Canny('initChangelog', {
            appID: '6267ca97f968c052891e7f8b',
            position: 'top',
            align: 'left'
          })
        })
    }
  }
}
</script>

<style>
.Canny_Badge {
  background-color: #3B82F6 !important;
  top: 5px !important;
  right: 12px !important;
}
</style>
