<template>
  <editor-options-panel name="Notifications & Integrations" :already-opened="true" :has-pro-tag="true">
    <template #icon>
      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6M22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </template>

    <form-notifications-option class="mt-2" />
    <form-notifications-submission-confirmation />
    <form-notifications-slack />
    <form-notifications-discord />
    <form-notifications-webhook />
    <v-button color="white"
      class="flex items-center mt-3 cursor-pointer relative w-full rounded-lg flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      :href="zapierUrl" target="_blank"
    >
      <div class="flex-grow flex items-center">
        <svg class="w-5 h-5 inline text-yellow-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
          <path
            d="M318 256c0 19-4 36-10 52-16 7-34 10-52 10-19 0-36-3-52-9-7-17-10-34-10-53 0-18 3-36 10-52 16-6 33-10 52-10 18 0 36 4 52 10 6 16 10 34 10 52zm182-41H355l102-102c-8-11-17-22-26-32-10-9-21-18-32-26L297 157V12c-13-2-27-3-41-3s-28 1-41 3v145L113 55c-12 8-22 17-32 26-10 10-19 21-27 32l102 102H12s-3 27-3 41 1 28 3 41h144L54 399c16 23 36 43 59 59l102-102v144c13 2 27 3 41 3s28-1 41-3V356l102 102c11-8 22-17 32-27 9-10 18-20 26-32L355 297h145c2-13 3-27 3-41s-1-28-3-41z"
          />
        </svg>
        <p class="flex-grow text-center font-normal">
          Zapier Integration
        </p>
      </div>
    </v-button>

  </editor-options-panel>
</template>

<script>
import EditorOptionsPanel from '../../../editors/EditorOptionsPanel.vue'
import FormNotificationsOption from './components/FormNotificationsOption.vue'
import FormNotificationsSlack from './components/FormNotificationsSlack.vue'
import FormNotificationsDiscord from './components/FormNotificationsDiscord.vue'
import FormNotificationsSubmissionConfirmation from './components/FormNotificationsSubmissionConfirmation.vue'
import FormNotificationsWebhook from './components/FormNotificationsWebhook.vue'
import opnformConfig from "~/opnform.config.js";

export default {
  components: { FormNotificationsSubmissionConfirmation, FormNotificationsSlack, FormNotificationsDiscord, FormNotificationsOption, EditorOptionsPanel, FormNotificationsWebhook },
  props: {},
  setup () {
    const workingFormStore = useWorkingFormStore()
    const {content: form} = storeToRefs(workingFormStore)
    return {
      workingFormStore,
      form,
      opnformConfig
    }
  },

  computed: {
    zapierUrl () {
      opnformConfig.links.zapier_integration
    }
  },
}
</script>
