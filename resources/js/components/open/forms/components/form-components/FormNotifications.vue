<template>
  <collapse class="p-4 w-full border-b" v-model="isCollapseOpen">
    <template #title>
      <h3 id="v-step-2" class="font-semibold text-lg">
        <svg class="h-5 w-5 inline mr-2 transition-colors"
             :class="{'text-blue-600':isCollapseOpen, 'text-gray-500':!isCollapseOpen}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6M22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        Notifications
        <pro-tag />
      </h3>
    </template>

    <form-notifications-option class="mt-2" />
    <form-notifications-submission-confirmation />
    <form-notifications-slack />
    <form-notifications-discord />

  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse.vue'
import ProTag from '../../../../common/ProTag.vue'
import FormNotificationsOption from './components/FormNotificationsOption.vue'
import FormNotificationsSlack from './components/FormNotificationsSlack.vue'
import FormNotificationsDiscord from './components/FormNotificationsDiscord.vue'
import FormNotificationsSubmissionConfirmation from './components/FormNotificationsSubmissionConfirmation.vue'

export default {
  components: { FormNotificationsSubmissionConfirmation, FormNotificationsSlack, FormNotificationsDiscord, FormNotificationsOption, Collapse, ProTag },
  props: {
  },
  data () {
    return {
      isCollapseOpen: true
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

  watch: {
  },

  mounted () {
  },
}
</script>
