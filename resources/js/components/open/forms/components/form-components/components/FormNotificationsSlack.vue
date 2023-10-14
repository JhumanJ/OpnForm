<template>
  <div>
    <button
      class="flex items-center mt-3 cursor-pointer relative w-full rounded-lg flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click.prevent="showModal=true"
    >
      <div class="flex-grow flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z" /><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" /><path d="M9.5 14c.83 0 1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5S8 21.33 8 20.5v-5c0-.83.67-1.5 1.5-1.5z" /><path d="M3.5 14H5v1.5c0 .83-.67 1.5-1.5 1.5S2 16.33 2 15.5 2.67 14 3.5 14z" /><path d="M14 14.5c0-.83.67-1.5 1.5-1.5h5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-5c-.83 0-1.5-.67-1.5-1.5z" /><path d="M15.5 19H14v1.5c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z" /><path d="M10 9.5C10 8.67 9.33 8 8.5 8h-5C2.67 8 2 8.67 2 9.5S2.67 11 3.5 11h5c.83 0 1.5-.67 1.5-1.5z" /><path d="M8.5 5H10V3.5C10 2.67 9.33 2 8.5 2S7 2.67 7 3.5 7.67 5 8.5 5z" /></svg>
        <p class="flex-grow text-center">
          Slack Notifications
        </p>
      </div>
      <div v-if="form.notifies_slack">
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
        Slack Notifications
        <pro-tag />
      </h2>
      <toggle-switch-input name="notifies_slack" :form="form" class="mt-4"
                           label="Receive a Slack notification on submission"
      />
      <template v-if="form.notifies_slack">
        <text-input name="slack_webhook_url" :form="form" class="mt-4"
                    label="Slack webhook url" help="help"
        >
          <template #help>
            Receive slack message on each form submission. <a href="https://api.slack.com/messaging/webhooks"
                                                              target="_blank"
            >Click here</a> to learn how to get a slack
            webhook url
          </template>
        </text-input>
        <h4 class="font-bold mt-4">Slack message actions</h4>
        <form-notifications-message-actions v-model="form.notification_settings.slack"  />
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
    if(!this.form.notification_settings.slack || Array.isArray(this.form.notification_settings.slack)){
      this.form.notification_settings.slack = {}
    }
  },

  methods: {}
}
</script>
