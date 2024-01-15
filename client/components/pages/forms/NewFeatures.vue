<template>
  <div id="new-features"
       class="w-full bg-gray-50 dark:bg-gray-800 border rounded-lg mt-4"
  >
    <div class="border-b">
      <div v-track.new_in_notionforms_click
           class="relative flex items-center cursor-pointer hover:bg-gray-100 p-4" role="button"
           @click.prevent="showNewFeatures=!showNewFeatures"
      >
        <div class="text-gray-700 dark:text-gray-300 pr-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2"
          >
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
            />
          </svg>
        </div>
        <div>
          <p class="text-gray-700 dark:text-gray-300 font-semibold">
            New in OpnForm
          </p>
          <p class="text-sm text-gray-700 dark:text-gray-300">
            Click here to see our new features
          </p>
        </div>
      </div>
      <v-transition>
        <ul v-if="showNewFeatures" class="list-disc list-inside border-t pt-2 p-4">
          <li v-for="changelog in changelogEntries" :key="changelog.id" v-track.new_feature_click class="text-sm">
            <a :href="changelog.url" target="_blank" class="text-gray-700 dark:text-gray-300">{{ changelog.title }}</a>
          </li>
          <li v-track.new_feature_read_more_click class="text-sm">
            <a class="text-gray-700 dark:text-gray-300" :href="changelogLink" target="_blank">Read more</a>
          </li>
        </ul>
      </v-transition>
    </div>
    <div class="relative flex items-center cursor-pointer hover:bg-gray-100 p-4">
      <a v-track.feature_request_click="{user_has_forms:user.has_forms}" :href="requestFeatureLink"
         class="absolute inset-0" target="_blank"
      />
      <div class="text-gray-700 dark:text-gray-300 pr-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
             stroke-width="2"
        >
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
          />
        </svg>
      </div>
      <div>
        <p class="text-gray-700 dark:text-gray-300 font-semibold">
          An idea for a new feature?
        </p>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Click here to request a new feature
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'
import { useAuthStore } from '../../../stores/auth';
import VTransition from '~/components/global/transitions/VTransition.vue'

export default {
  components: { VTransition },
  props: {},

  setup () {
    const authStore = useAuthStore()
    return {
      user : computed(() => authStore.user)
    }
  },

  data: () => ({
    changelogEntries: [],
    showNewFeatures: false
  }),

  mounted () {
    this.loadChangelogEntries()
  },

  computed: {
    requestFeatureLink () {
      return this.$config.links.feature_requests
    },
    changelogLink () {
      return this.$config.links.changelog_url
    }
  },

  methods: {
    loadChangelogEntries () {
      opnFetch('/content/changelog/entries').then(data => {
        this.changelogEntries = data.splice(0, 3)
      })
    }
  }
}
</script>
