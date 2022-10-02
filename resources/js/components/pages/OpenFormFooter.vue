<template>
  <div class="w-full bg-gray-50 dark:bg-notion-dark p-10">
    <div class="px-4 py-6 w-full md:max-w-3xl md:mx-auto md:px-24 lg:px-8">
      <div class="flex flex-wrap">
        <div class="md:max-w-md lg:col-span-2 mr-2 pr-2">
          <a href="/" aria-label="Go home" title="Company" class="inline-flex items-center">
            <img :src="asset('img/logo.svg')" alt="notion tools logo" class="w-8 h-8 inline">
            <span class="ml-2 text-xl font-bold tracking-wide text-gray-800 dark:text-gray-200">OpnForm</span>
          </a>
        </div>
        <div class="grid grid-cols-2 gap-5 row-gap-8 lg:col-span-4 md:grid-cols-3 mt-8 lg:mt-0">
          <div>
            <p class="font-semibold tracking-wide text-gray-800 dark:text-gray-200">
              Resources
            </p>
            <ul class="mt-2 space-y-2">
<!--              <li>-->
<!--                <router-link :to="{name:'pricing'}"-->
<!--                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"-->
<!--                >-->
<!--                  Pricing-->
<!--                </router-link>-->
<!--              </li>-->
              <li>
                <router-link :to="{name:'templates'}"
                    class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >
                  Templates
                </router-link>
              </li>
              <li>
                <a target="_blank" :href="helpUrl"
                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >
                  Help
                </a>
              </li>
<!--              <li>-->
<!--                <router-link :to="{name:'guides'}"-->
<!--                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"-->
<!--                >-->
<!--                  Guides-->
<!--                </router-link>-->
<!--              </li>-->
              <li>
                <router-link :to="{name:'integrations'}"
                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >
                  Integrations
                </router-link>
              </li>
<!--              <li id="changelog" data-canny-changelog class="relative block">-->
<!--                <p id="changelog-trigger"-->
<!--                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue cursor-pointer"-->
<!--                >-->
<!--                  Product Updates-->
<!--                </p>-->
<!--              </li>-->
              <li class="relative block">
                <a target="_blank" :href="featureRequestsUrl"
                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >
                  Feature Requests
                </a>
              </li>
            </ul>
          </div>
          <div>
            <p class="font-semibold tracking-wide text-gray-800 dark:text-gray-200">
              Community
            </p>
            <ul class="mt-2 space-y-2">
              <li>
                <a target="_blank" :href="facebookGroupUrl"
                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >Facebook
                  Group</a>
              </li>
              <li>
                <a target="_blank" :href="twitterUrl"
                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >Twitter</a>
              </li>
              <li>
                <a target="_blank" :href="githubUrl"
                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >GitHub</a>
              </li>
              <li>
                <a target="_blank" :href="forumUrl"
                   class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >Forum</a>
              </li>
<!--              <li>-->
<!--                <router-link :to="{name:'discount-community'}"-->
<!--                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"-->
<!--                >-->
<!--                  Academic Discount-->
<!--                </router-link>-->
<!--              </li>-->
<!--              <li>-->
<!--                <router-link :to="{name:'discount-community'}"-->
<!--                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"-->
<!--                >-->
<!--                  NGO Discount-->
<!--                </router-link>-->
<!--              </li>-->
<!--              <li>-->
<!--                <router-link :to="{name:'partners'}"-->
<!--                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"-->
<!--                >-->
<!--                  Our Partners-->
<!--                </router-link>-->
<!--              </li>-->
<!--              <li>-->
<!--                <router-link :to="{name:'ambassadors'}"-->
<!--                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"-->
<!--                >-->
<!--                  Notion Ambassadors-->
<!--                </router-link>-->
<!--              </li>-->
            </ul>
          </div>
          <div class="ml-auto">
            <p class="font-semibold tracking-wide text-gray-800 dark:text-gray-200">
              Legal
            </p>
            <ul class="mt-2 space-y-2">
              <li>
                <router-link :to="{name:'privacy-policy'}"
                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >
                  Privacy Policy
                </router-link>
              </li>
              <li>
                <router-link :to="{name:'terms-conditions'}"
                             class="text-gray-600 dark:text-gray-400 transition-colors duration-300 hover:text-nt-blue"
                >
                  Terms & Conditions
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="flex flex-col justify-between pt-5 pb-10 border-t mt-4 sm:flex-row">
        <p class="text-sm text-gray-600 dark:text-gray-400 text-center w-full">
          © Copyright 2022 OpnForm. All rights reserved.
        </p>
      </div>
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
