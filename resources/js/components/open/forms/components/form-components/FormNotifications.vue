<template>
  <collapse class="py-3 w-full border-b" :default-value="isCollapseOpen" @click="onClickCollapse">
    <template #title>
      <h3 id="v-step-2" class="font-semibold text-lg">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2 -mt-1" 
          :class="{'text-blue-600':isCollapseOpen, 'text-gray-500':!isCollapseOpen}"
          fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        Notifications
        <pro-tag />
      </h3>
    </template>
    <checkbox-input name="notifies" :form="form" class="mt-4"
                    label="Receive email notifications on submission"
    />
    <text-area-input v-if="form.notifies" name="notification_emails" :form="form" class="mt-4"
                     label="Notification Emails" help="Add one email per line"
    />
    <checkbox-input name="notifies_slack" :form="form" class="mt-4"
                    label="Receive a Slack notification on submission"
    />
    <text-input v-if="form.notifies_slack" name="slack_webhook_url" :form="form" class="mt-4"
                label="Slack webhook url" help="help"
    >
      <template #help>
        Receive slack message on each form submission. <a href="https://api.slack.com/messaging/webhooks" target="_blank">Click here</a> to learn how to get a slack webhook url
      </template>
    </text-input>
    
    <checkbox-input :disabled="emailSubmissionConfirmationField===null" name="send_submission_confirmation"
                    :form="form" class="mt-4"
                    label="Send submission confirmation" :help="emailSubmissionConfirmationHelp"
    />
    <text-input v-if="form.send_submission_confirmation" name="notification_sender"
                    :form="form" class="mt-4"
                    label="Confirmation Email Sender Name" help="Emails will be sent from our email address but you can customize the name of the Sender"
    />
    <text-input v-if="form.send_submission_confirmation" name="notification_subject"
                    :form="form" class="mt-4"
                    label="Confirmation email subject" help="Subject of the confirmation email that will be sent"
    />
    <rich-text-area-input v-if="form.send_submission_confirmation" name="notification_body"
                    :form="form" class="mt-4"
                    label="Confirmation email content" help="Content of the confirmation email that will be sent"
    />
    <checkbox-input v-if="form.send_submission_confirmation" name="notifications_include_submission"
                    :form="form" class="mt-4"
                    label="Include submission data" help="If enabled the confirmation email will contain form submission answers"
    />
  </collapse>
</template>

<script>
import Collapse from '../../../../common/Collapse'
import ProTag from '../../../../common/ProTag'

export default {
  components: { Collapse, ProTag },
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
    },
    emailSubmissionConfirmationField () {
      const emailFields = this.form.properties.filter((field) => {
        return field.type === 'email' && !field.hidden
      })
      if (emailFields.length === 1) return emailFields[0]
      return null
    },
    emailSubmissionConfirmationHelp () {
      if (this.emailSubmissionConfirmationField) {
        return 'Confirmation will be sent to the email in the "' + this.emailSubmissionConfirmationField.name + '" field.'
      }
      return 'Only available if your form contains 1 email field.'
    }
  },

  watch: {
    emailSubmissionConfirmationField (val) {
      if (val === null) {
        this.$set(this.form, 'send_submission_confirmation', false)
      }
    }
  },

  mounted () {
  },

  methods: {
    onClickCollapse (e) {
      this.isCollapseOpen = e
    }
  }
}
</script>
