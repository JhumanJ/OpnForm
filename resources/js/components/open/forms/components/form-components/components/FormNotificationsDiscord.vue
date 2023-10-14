<template>
  <div>
    <button
      class="flex items-center mt-3 cursor-pointer relative w-full rounded-lg flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click.prevent="showModal=true"
    >
      <div class="flex-grow flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M7.5 7.5c3.5 -1 5.5 -1 9 0"></path><path d="M7 16.5c3.5 1 6.5 1 10 0"></path><path d="M15.5 17c0 1 1.5 3 2 3c1.5 0 2.833 -1.667 3.5 -3c.667 -1.667 .5 -5.833 -1.5 -11.5c-1.457 -1.015 -3 -1.34 -4.5 -1.5l-1 2.5"></path><path d="M8.5 17c0 1 -1.356 3 -1.832 3c-1.429 0 -2.698 -1.667 -3.333 -3c-.635 -1.667 -.476 -5.833 1.428 -11.5c1.388 -1.015 2.782 -1.34 4.237 -1.5l1 2.5"></path></svg>
        <p class="flex-grow text-center">
          Discord Notifications
        </p>
      </div>
      <div v-if="form.notifies_discord">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 text-nt-blue"
        >
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
      </div>
    </button>
    <modal :show="showModal" @close="showModal=false">
      <h2 class="text-2xl font-bold z-10 truncate mb-5 text-nt-blue">
        Discord Notifications
        <pro-tag />
      </h2>
      <toggle-switch-input name="notifies_discord" :form="form" class="mt-4"
                           label="Receive a Discord notification on submission"
      />
      <template v-if="form.notifies_discord">
        <text-input name="discord_webhook_url" :form="form" class="mt-4"
                    label="Discord webhook url" help="help"
        >
          <template #help>
            Receive a discord message on each form submission.
            <a href="https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks" target="_blank">Click
              here</a> to learn how to get a discord webhook url.
          </template>
        </text-input>
        <h4 class="font-bold mt-4">Discord message actions</h4>
        <form-notifications-message-actions v-model="form.notification_settings.discord"  />
      </template>
    </modal>
  </div>
</template>

<script>
import ProTag from '../../../../../common/ProTag.vue'
import FormNotificationsMessageActions from './FormNotificationsMessageActions.vue'

export default {
  components: { ProTag, FormNotificationsMessageActions },
  props: {},
  data () {
    return {
      showModal: false
    }
  },

  computed: {
    form: {
      get () {
        return this.$store.state['open/working_form'].content
      },
      /* We add a setter */
      set (value) {
        this.$store.commit('open/working_form/set', value)
      }
    }
  },

  watch: {},

  mounted () {
    if(!this.form.notification_settings.discord || Array.isArray(this.form.notification_settings.discord)){
      this.form.notification_settings.discord = {}
    }
  },

  methods: {}
}
</script>
