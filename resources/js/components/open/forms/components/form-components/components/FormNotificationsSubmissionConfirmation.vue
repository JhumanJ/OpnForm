<template>
  <div>
    <button
      class="flex items-center mt-3 cursor-pointer relative w-full rounded-lg flex-1 appearance-none border border-gray-300 dark:border-gray-600 w-full py-2 px-4 bg-white text-gray-700 dark:bg-notion-dark-light dark:text-gray-300 dark:placeholder-gray-500 placeholder-gray-400 shadow-sm text-base focus:outline-none focus:ring-2 focus:border-transparent focus:ring-opacity-100"
      @click.prevent="showModal=true"
    >
      <div class="flex-grow flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
        </svg>
        <p class="flex-grow text-center">
          Send submission confirmation
        </p>
      </div>
      <div v-if="form.send_submission_confirmation">
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
        Submission confirmation
        <pro-tag />
      </h2>
      <toggle-switch-input :disabled="emailSubmissionConfirmationField===null" name="send_submission_confirmation"
                           :form="form" class="mt-4"
                           label="Send submission confirmation" :help="emailSubmissionConfirmationHelp"
      />
      <template v-if="form.send_submission_confirmation">
        <text-input name="confirmation_reply_to"
                    v-model="form.notification_settings.confirmation_reply_to" class="mt-4"
                    label="Confirmation Reply To" help="help"
        >
          <template #help>
            If empty, Reply-to will be your own email.
          </template>
        </text-input>
        <text-input name="notification_sender"
                    :form="form" class="mt-4"
                    label="Confirmation Email Sender Name" help="Emails will be sent from our email address but you can customize the name of the Sender"
        />
        <text-input name="notification_subject"
                    :form="form" class="mt-4"
                    label="Confirmation email subject" help="Subject of the confirmation email that will be sent"
        />
        <rich-text-area-input name="notification_body"
                              :form="form" class="mt-4"
                              label="Confirmation email content" help="Content of the confirmation email that will be sent"
        />
        <toggle-switch-input name="notifications_include_submission"
                        :form="form" class="mt-4"
                        label="Include submission data" help="If enabled the confirmation email will contain form submission answers"
        />
      </template>
    </modal>
  </div>
</template>

<script>
import ProTag from '../../../../../common/ProTag.vue'

export default {
  components: { ProTag },
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
    },
    emailSubmissionConfirmationField () {
      if (!this.form.properties || !Array.isArray(this.form.properties)) return null
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

  methods: {}
}
</script>
